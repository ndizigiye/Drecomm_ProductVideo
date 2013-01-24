<?php
/**
 * Main block class, that controls the upload and the view of the videos
 * @package    Block
 * @author ndizigiye
 */
class Drecomm_ProductVideo_Block_VideoView
extends Mage_Core_Block_Template
{
    /**
     * collection of videos
     * @var $_collection Drecomm_ProductVideo_Model_Resource_Video_Collection
     */
    private $_collection ;

    /**
     * global helper object
     * @var $_global_helper Drecomm_ProductVideo_Helper_Global
     */
    private $_global_helper ;

    public function __construct(){
        $this->_collection = Mage::getResourceModel('drecomm_productvideo/video_collection');
        $this->_global_helper = Mage::Helper('drecomm_productvideo/global');
    }

    /**
     * Get the video urls from the database
     * @return array an array of video urls
     */
    public function getVideosUrl()
    {
        $videos = array();
        foreach ($this->_collection as $video)
        {
            if($video->getProductid() == Mage::registry('current_product')->getId() && $video->getUrl())
            {
                $videos[] = $video->getUrl();
            }
        }

        return $videos;
    }

    /**
     * Get the video ids from the database
     * @return array an array of video ids
     */
    public function getVideos()
    {
        foreach ($this->_collection as $video)
        {
            if($video->getProductid() == Mage::registry('current_product')->getId() && $video->getUrl())
            {
                $videos[] = $video;
            }
        }

        return $videos;
    }

    /**
     * Get an thumbnail from a Youtube Url
     * @param string $videoUrl video url
     * @return string thumbnail url
     */
    public function getYoutubeVideoThumbnail($videoUrl)
    {
        return 'http://img.youtube.com/vi/'.$this->_global_helper->getYoutubeVideoId($videoUrl).'/default.jpg';
    }

    /**
     * Get an thumbnail from a Vimeo Url
     * @param string $videoUrl video url
     * @return string thumbnail url
     */
    public function getVimeoVideoThumbnail($videoUrl)
    {
        $hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/'.$this->_global_helper->getVimeoVideoId($videoUrl).'.php'));

        return $hash[0]["thumbnail_medium"];
    }
}

