<?php
/**
 * Renders the form html in the backend
 * @author ndizigiye
 * @package    Block
 * @author ndizigiye
 */
class Drecomm_ProductVideo_Block_Adminhtml_Video_Edit_Form
extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * The id of a video
     * @var string
     */
    private $_videoId;

    /**
     * The id of the current product
     * @var string
     */
    private $_productId;

    /**
     * Init class
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('drecomm_productvideo_video_form');
        $this->setTitle($this->__('Video Information'));
        $this->_videoId = $this->getRequest()->getParam('videoid');
        $this->_productId = $this->getRequest()->getParam('productid');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $global_helper = $this->helper('drecomm_productvideo/global');
        $videoModel = Mage::getModel('drecomm_productvideo/video');
        $productModel = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('productid'));
        $productName = $productModel->getName();
        $videoModel->load($this->_videoId);
        $videoUrl = $videoModel->getUrl();
        $videoFormat = $global_helper->getVideoFormat($videoUrl);
        $videoFormat == 'youtube' ?
        $embedded = '<iframe width="460" height="215"
                src="http://www.youtube.com/embed/'.str_replace(" [uploaded]","",$global_helper->getYoutubeVideoId($videoUrl)).'"
                        frameborder="0" allowfullscreen>
                        </iframe>'
                        :
                        $embedded = '<iframe width="500" height="281"
                                src="http://player.vimeo.com/video/'.$global_helper->getVimeoVideoId($videoUrl).'"
                                        frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen>
                                        </iframe>';

        if ($this->getRequest()->getParam('upload'))
        {
            $apiUrls = $global_helper->createApiFormUrls($productName);
            $status = $_GET['status'];
            $code = $_GET['code'];
            $id = $_GET['id'];

            if(isset($status))
            {
                $code = $code;
                $id = $id;
            }
            $uploadResponse = $global_helper->uploadResponse($status,$code,$id);
            $postUrl = $apiUrls['postUrl'];
            $tokenValue  = $apiUrls['token'];
            $nextUrl = Mage::helper("adminhtml")->getUrl("*/video/new/", array(
                    'productid' => $this->_productId , "upload" => "true"));
            $uploadStatus = isset($_GET['status']) ? $_GET['status'] : null ;

            $form = new Varien_Data_Form(array(
                    'id'        => 'edit_form',
                    'action'    => $postUrl.'?nexturl='.$nextUrl,
                    'method'    => 'post',
                    'enctype'   => 'multipart/form-data'
            ));
            $fieldset = $form->addFieldset('base_fieldset', array(
                    'legend'    => Mage::helper('checkout')->__('Video Information'),
                    'class'     => 'fieldset-wide',
            ));

            if ($videoModel->getId())
            {
                $fieldset->addField('videoid', 'hidden', array(
                        'name' => 'videoid',
                ));
            }
            $fieldset->addField('productId', 'hidden', array(
                    'name' => 'productId',
            ));
            $fieldset->addField('url', 'hidden', array(
                    'name' => 'url',
            ));

            $fieldset->addField('file', 'file', array(
                    'name'      => 'file',
                    'label'     => Mage::helper('checkout')->__('Video file'),
                    'title'     => Mage::helper('checkout')->__('Video Url'),
                    'required'  => false,
                    'onchange' => "onChange(document.getElementById('file').value)",
                    'after_element_html' => '
                    <input name="token" type="hidden" value="'.$tokenValue.'"/>
                    <button  id="uploadButton"
                    title="Save Video" type="button"
                    class="scalable save"
                    onclick="changeDivVisibility();submit();"
                    style=""><span><span><span>upload</span></span></span>
                    </button>
                    <p id="uploadStatus" style="display:none">'.$uploadStatus.'</p>
                    <script>setUrl();</script>
                    <div id="uploading" style="display:none">
                    <p class="loader" id="loading_mask_loader">
                    <img src="'.$this->getSkinUrl('images/ajax-loader-tr.gif') .'" alt="'.Mage::helper('adminhtml')->__('Loading...').'"/>
                    <br/>'.Mage::helper('adminhtml')->__('Uploading, please wait...').'</p>
                    </div>'
                    ,
            ));

            if(isset($_GET['id']))
            {
                $fieldset->addField('saveupload', 'hidden', array(
                        'name' => 'saveupload',
                        'after_element_html' =>'
                        <script>window.location="'.$this->getUrl('*/video/save', array
                                (
                                        'form_key' => Mage::getSingleton('core/session')->getFormKey(),
                                        'videoid' => $this->_videoId,
                                        'productid' => $this->_productId,
                                        'url' => $_GET['id'],
                                        'upload' => 'true',)
                        ).'";$j("#uploading").css("display", "block");</script>
                        '
                ));
            }
            $form->setValues($videoModel->getData());
            $form->setUseContainer(true);
            $this->setForm($form);
        }
        else
        {
            $form = new Varien_Data_Form(array(
                    'id'        => 'edit_form',
                    'action'    => $this->getUrl('*/*/save',
                            array(
                                    'videoid' => $this->_videoId,
                                    'productid' => $this->_productId
                            )),
                    'method'    => 'post',
                    'enctype' => 'multipart/form-data'
            ));
            $fieldset = $form->addFieldset('base_fieldset', array(
                    'legend'    => Mage::helper('checkout')->__('Url'),
                    'class'     => 'fieldset-wide',
            ));

            if ($videoModel->getId())
            {
                $fieldset->addField('videoid', 'hidden', array(
                        'name' => 'videoid',
                ));
            }

            $fieldset->addField('productId', 'hidden', array(
            ));

            $fieldset->addField('url', 'text', array(
                    'name'      => 'url',
                    'label'     => Mage::helper('checkout')->__('Video Url'),
                    'title'     => Mage::helper('checkout')->__('Video Url'),
                    'required'  => false,
                    'after_element_html' => '
                    </br><small> use a valid vimeo or youtube url</small>
                    <div id="loading-mask" style="display:none">
                    <p class="loader" id="loading_mask_loader">
                    <img src="'.$this->getSkinUrl('images/ajax-loader-tr.gif') .'" alt="'.Mage::helper('adminhtml')->__('Loading...').'"/>
                    <br/>'.Mage::helper('adminhtml')->__('Please wait...').'</p>
                    </div>
                    ',
            ));

            if ($videoModel->getId())
            {
                $fieldset->addField('embed', 'hidden', array(
                        'name' => 'embed',
                        'after_element_html' => '<br><br>'.$embedded
                ));
            }

            $form->setValues($videoModel->getData());
            $form->setUseContainer(true);
            $this->setForm($form);
        }

        return parent::_prepareForm();
    }
}