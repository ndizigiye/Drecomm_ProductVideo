<?php
/**
 * Controller of the module
 * @package    controllers
 * @author ndizigiye
 */
class Drecomm_ProductVideo_Adminhtml_VideoController
extends Mage_Adminhtml_Controller_Action
{

    /**
     * The id of a video
     * @var string
     */
    private $_videoId;

    /**
     * The video model object
     * @var Video Drecomm_ProductVideo_Model_Video
     */
    private $_videoModel;

    /**
     * collection of video Ids
     * @var string
     */
    private $_videoIds;

    /**
     * The id of the current product
     * @var string
     */
    private $_productId;

    /**
     * the index action
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * the new action
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * the edit action
     * @return void
     */
    public function editAction()
    {
        if(($this->getRequest()->getParam('error')))
        {
            echo "<script type='text/javascript'>alert('Invalid URL, please use a valid url!')</script>";
        }
        $this->_initAction();

        if ($this->_videoId)
        {
            $this->_videoModel->load($this->_videoId);

            if (!$this->_videoModel->getId())
            {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This video no longer exists.'));
                $this->_redirect('*/catalog_product/edit/id/'.$this->_productId.'/back/edit/tab/product_info_tabs_videos/');
            }
        }

        $this->_title($this->_videoModel->getId() ? $this->_videoModel->getName() : $this->__('New Video'));
        $data = Mage::getSingleton('adminhtml/session')->getVideoData(true);
        if (!empty($data))
        {
            $this->_videoModel->setData($data);
        }

        Mage::register('drecomm_productvideo', $this->_videoModel);
        $this->_initAction()
        ->_addBreadcrumb($this->_videoId ? $this->__('Edit Video') : $this->__('New Video'),$this->__('Videos'), $this->_videoId ? $this->__('Edit Video') : $this->__('New Video'))
        ->_addContent($this->getLayout()->createBlock('drecomm_productvideo/adminhtml_video_edit')->setData('action', $this->getUrl('*/*/save')))
        ->renderLayout();
    }

    /**
     * the delete action
     * @return void
     */
    public function deleteAction()
    {
        $this->_initAction();
        if( $this->_videoId > 0 )
        {
            try
            {
                if($this->getRequest()->getParam('deleteYoutube'))
                {
                    $global_helper = Mage::Helper('drecomm_productvideo/global');
                    $global_helper->deleteVideo(str_replace(" [uploaded]","",$this->_videoModel->load($this->_videoId)->getUrl()));
                }
                $this->_videoModel->setId($this->_videoId)
                ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Video was successfully deleted'));
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->_videoId));
            }
        }
        $this->_redirect('*/catalog_product/edit/id/'.$this->_productId.'/back/edit/tab/product_info_tabs_videos/');
    }

    /**
     * check whether the given url is valid
     * @param string $url
     * @return boolean
     */
    public function isValidUrl($url)
    {
        return preg_match('/^((http|https):\/\/(www.)*(vimeo.com\/(([a-z0-9]{2,30})\/){0,4}([0-9]{7,8})|youtu.be\/[a-zA-Z0-9_-]{11}|youtube.com\/watch\?v=[a-zA-Z0-9_-]{11}(&([a-zA-Z0-9_-]){0,10}=([a-zA-Z0-9_-]){0,10}){0,10}))$/i', $url);
    }

    /**
     * the save action
     * @return void
     */
    public function saveAction()
    {
        $this->_initAction();
        if(!$this->isValidUrl($this->getRequest()->getPost('url')) and !$this->getRequest()->getParam('upload'))
        {
            $this->_redirect('*/video/edit/videoid/'.$this->_videoId.'/productid/'.$this->_productId.'/error/true');
        }
        else if ($postData = $this->getRequest()->getPost())
        {
            if($this->_videoId){
                $this->_videoModel->load($this->_videoId);
            }
            $this->_videoModel->setData($postData);
            $this->_videoModel->setData('productid',$this->_productId); // set the productId to the current productID
            try
            {
                if($this->_videoId){
                    $this->_videoModel->setId($this->_videoId);
                }
                $this->_videoModel->save();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The video(s) has been saved.'));
                $this->_redirect('*/catalog_product/edit/id/'.$this->_productId.'/back/edit/tab/product_info_tabs_videos/');
                return;
            }
            catch (Mage_Core_Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this video.'));
            }
            Mage::getSingleton('adminhtml/session')->setVideoData($postData);
            $this->_redirectReferer();
        }
        else
        {
            $postData = array(
                    'form_key' => $this->getRequest()->getParam('form_key'),
                    'videoid' => $this->_videoId,
                    'productid' => $this->_productId,
                    // add  [uploaded] to distinguish uploaded videos.
                    'url' => 'http://www.youtube.com/watch?v='.$this->getRequest()->getParam('url').' [uploaded]',
            );

            if($this->_videoId){
                $this->_videoModel->load($this->_videoId);
            }
            $this->_videoModel->setData($postData);
            $this->_videoModel->setData('productid',$this->_productId); // set the productId to the current productID
            try
            {
                $this->_videoModel->save();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The video(s) has been saved.'));
                $this->_redirect('*/catalog_product/edit/id/'.$this->_productId.'/back/edit/tab/product_info_tabs_videos/');

                return;
            }
            catch (Mage_Core_Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this video.'));
            }
            Mage::getSingleton('adminhtml/session')->setVideoData($postData);
            $this->_redirectReferer();
        }
    }

    /**
     * the mass delete action
     * @return void
     */
    public function massDeleteAction()
    {
        $this->_initAction();
        try
        {
            foreach ($this->_videoIds as $videoId)
            {
                $this->_videoModel->setId(intval($videoId))->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Video was successfully deleted'));
            $this->_redirect('*/catalog_product/edit/id/'.$this->_productId.'/back/edit/tab/product_info_tabs_videos/');
        }catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    /**
     * the mass enable action
     * @return void
     */
    public function massEnableAction()
    {
        $this->_initAction();
        try
        {
            foreach ($this->_videoIds as $videoId)
            {
                $this->_videoModel->load(intval($videoId))->setStatus('enabled');
                $this->_videoModel->save();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Video(s) enabled'));
            $this->_redirect('*/catalog_product/edit/id/'.$this->_productId.'/back/edit/tab/product_info_tabs_videos/');
        }catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    /**
     * the mass disable action
     * @return void
     */
    public function massDisableAction()
    {
        $this->_initAction();
        try
        {
            foreach ($this->_videoIds as $videoId)
            {
                $this->_videoModel->load(intval($videoId))->setStatus('disabled');
                $this->_videoModel->save();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Video(s) disabled'));
            $this->_redirect('*/catalog_product/edit/id/'.$this->_productId.'/back/edit/tab/product_info_tabs_videos/');
        }catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    /**
     * the message action
     * @return void
     */
    public function messageAction()
    {
        $this->_initAction();
        $data = $this->_videoModel->load();
        echo $data->getContent();
    }

    /**
     * initialize basic configuration
     * @return void
     */
    protected function _initAction()
    {
        $this->_videoId = $this->getRequest()->getParam('videoid');
        $this->_productId = $this->getRequest()->getParam('productid');
        $this->_videoIds = $this->getRequest()->getParam('video');
        $this->_videoModel = Mage::getSingleton('drecomm_productvideo/video');
        $this->loadLayout()
        ->_title($this->__('Video'))
        ->_addBreadcrumb($this->_videoId ? $this->__('Edit Video') : $this->__('New Video'),$this->__('Videos'), $this->_videoId ? $this->__('Edit Video') : $this->__('New Video'));

        return $this;
    }

    /**
     * Check currently called action by permissions for current user
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/drecomm_productvideo_video');
    }
}