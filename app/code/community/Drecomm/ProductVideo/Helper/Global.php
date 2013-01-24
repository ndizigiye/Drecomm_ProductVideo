<?php
/**
 * Helper used both in the frontend and backend
 * @package    Helper
 * @author ndizigiye
 *
 */
class Drecomm_ProductVideo_Helper_Global extends Mage_Core_Helper_Abstract
{

    /**
     * collection of videos
     * @var $_collection Drecomm_ProductVideo_Model_Resource_Video_Collection
     */
    private $_collection ;

    public function __construct(){
        $this->_collection = Mage::getResourceModel('drecomm_productvideo/video_collection');
    }

    /**
     * Get the video provider (youtube.com or vimeo.com)
     * @param string $videourl
     * @return string videotype(vimeo or youtube)
     */
    public function getVideoFormat($videourl)
    {
        $format = (stristr($videourl,'youtube') or stristr($videourl,'youtu.be')) ? 'youtube' : 'vimeo';

        return $format;
    }

    /**
     * Get the videoid from a youtube url
     * @param string $videoUrl
     * @return string the video id of the given url
     */
    public function getYoutubeVideoId($videoUrl)
    {
        $videoUrl = str_replace(" [uploaded]", "",$videoUrl);
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $videoUrl, $match);

        return $match[0];
    }

    /**
     * Get the videoid from a vimeo url
     * @param string $videoUrl
     * @return string the video id of the given url
     */
    public function getVimeoVideoId($videoUrl)
    {
        preg_match('/(\d+)/', $videoUrl, $match);
        if(isset($videoUrl)){
            return $match[0];
        }
    }

    /**
     * Get an thumbnail from a Youtube Url
     * @param string $videoUrl video url
     * @return string thumbnail url
     */
    public function getYoutubeVideoThumbnail($videoUrl)
    {
        return 'http://img.youtube.com/vi/'.$this->getYoutubeVideoId($videoUrl).'/default.jpg';
    }

    /**
     * Get an thumbnail from a Vimeo Url
     * @param string $videoUrl video url
     * @return string thumbnail url
     */
    public function getVimeoVideoThumbnail($videoUrl)
    {
        $hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/'.$this->getVimeoVideoId($videoUrl).'.php'));

        return $hash[0]["thumbnail_medium"];
    }

    /**
     * makes a call to the youtube API and get the form action url and the token value back
     * @return array an array with the post url and token value
     */
    public function createApiFormUrls($title)
    {
        $developerKey = Mage::getStoreConfig('productvideo_youtube_api/youtube_api/productvideo_gmail_developerkey');
        $applicationId = 'Video uploader v2';
        $authenticationURL= 'https://www.google.com/accounts/ClientLogin';
        try{
            $httpClient = Zend_Gdata_ClientLogin::getHttpClient(
                    $username = Mage::getStoreConfig('productvideo_youtube_api/youtube_api/productvideo_gmail_email'),
                    $password = Mage::getStoreConfig('productvideo_youtube_api/youtube_api/productvideo_gmail_password'),
                    $service = 'youtube',
                    $client = null,
                    $source = 'store', // a short string identifying your application
                    $loginToken = null,
                    $loginCaptcha = null,
                    $authenticationURL);
        }
        catch(exception $e){
            print 'You cannot be authenticated, please provide valid Gmail account information in System->Configuration->Youtube Api';
            die();
        }
        $httpClient->setHeaders('X-GData-Key', 'key='. $developerKey);
        $videoTitle  = $title;
        $videoDescription= $title;
        $videoCategory= 'Howto';

        $youTubeService = new Zend_Gdata_YouTube($httpClient);
        $newVideoEntry = new Zend_Gdata_YouTube_VideoEntry();

        $newVideoEntry->setVideoTitle($videoTitle);
        $newVideoEntry->setVideoDescription($videoDescription);

        //make sure first character in category is capitalized
        $videoCategory = strtoupper(substr($videoCategory, 0, 1))
        . substr($videoCategory, 1);
        $newVideoEntry->setVideoCategory($videoCategory);

        // uncomment to add video tags to a video
        //$videoTagsArray = explode(' ', trim($videoTags));
        //$newVideoEntry->setVideoTags(implode(', ', $videoTagsArray));

        $tokenHandlerUrl = 'https://gdata.youtube.com/action/GetUploadToken';
        try {
            $tokenArray = $youTubeService->getFormUploadToken($newVideoEntry, $tokenHandlerUrl);
        } catch (Zend_Gdata_App_HttpException $httpException) {
            print 'ERROR ' . $httpException->getMessage()
            . ' HTTP details<br /><textarea cols="100" rows="20">'
                    . $httpException->getRawResponseBody()
                    . '</textarea><br />';
            return;
        } catch (Zend_Gdata_App_Exception $e) {
            $e->getMessage();
            return;
        }
        $tokenValue = $tokenArray['token'];
        $postUrl = $tokenArray['url'];
        $urls = array("postUrl" => $postUrl , "token" => $tokenValue);

        return $urls;
    }

    /**
     * check the upload status of the upload and return the video ID
     * @param int $status return 400 on succes
     * @param int $code code error when a video was not succesfully uploaded
     * @param string $videoId the video Id that is returned after a succes upload
     * @return string
     */
    function uploadResponse($status, $code = null, $videoId = null)
    {
        switch ($status)
        {
            case $status < 400:
                return  'Video succesfully uploaded: <div id="youtubelink">http://www.youtube.com/watch?v='.$videoId.'</div>';
                break;
            default:
                return 'There seems to have been an error: '. $code;
        }
    }

    /**
     * delete a video on youtube
     * @param string $videoId the id of a youtube video
     * @return void
     */
    function deleteVideo($videoId)
    {
        $videoId = $this->getYoutubeVideoId($videoUrl);
        $developerKey = Mage::getStoreConfig('productvideo_youtube_api/youtube_api/productvideo_gmail_developerkey');
        $applicationId = 'Video uploader v2';
        $authenticationURL= 'https://www.google.com/accounts/ClientLogin';
        try{
            $httpClient = Zend_Gdata_ClientLogin::getHttpClient(
                    $username = Mage::getStoreConfig('productvideo_youtube_api/youtube_api/productvideo_gmail_email'),
                    $password = Mage::getStoreConfig('productvideo_youtube_api/youtube_api/productvideo_gmail_password'),
                    $service = 'youtube',
                    $client = null,
                    $source = 'Magento Store', // a short string identifying the application
                    $loginToken = null,
                    $loginCaptcha = null,
                    $authenticationURL);
        }
        catch(exception $e){
            echo '<script>alert("You cannot be authenticated, please provide valid Gmail account information in System->Configuration->Youtube Api")</script>';
            echo '<script>history.back();</script>';
        }
        $httpClient->setHeaders('X-GData-Key', 'key='. $developerKey);
        $youTubeService = new Zend_Gdata_YouTube($httpClient);
        $videoEntryToDelete = $youTubeService->getFullVideoEntry($videoId);
        $youTubeService->delete($videoEntryToDelete);
        return;
    }

    /**
     * check the status of a video
     * @param string $videoUrl
     * @return string
     */
    function getStatus($videoUrl)
    {
        $videoStatus = "enabled";

        foreach ($this->_collection as $video)
        {
            if($video->getUrl() == $videoUrl)
            {
                $videoStatus = $video->getStatus();
                break;
            }
        }

        return $videoStatus;
    }
}