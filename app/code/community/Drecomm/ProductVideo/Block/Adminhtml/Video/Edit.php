<?php
/**
 * Form container class
 * @package    Block
 * @author ndizigiye
 */
class Drecomm_ProductVideo_Block_Adminhtml_Video_Edit
extends Mage_Adminhtml_Block_Widget_Form_Container
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
        $this->_videoId = $this->getRequest()->getParam("videoid");
        $this->_productId = $this->getRequest()->getParam("productid");
        $this->_blockGroup = 'drecomm_productvideo';
        $this->_controller = 'adminhtml_video';
        parent::__construct();

        if($this->getRequest()->getParam('upload'))
        {
            $this->removeButton('save');
        }

        if($this->_videoId)
        {
            $this->_addButton('delete', array(
                    'label'     => Mage::helper('adminhtml')->__('Delete Url'),
                    'class'     => 'delete',
                    'onclick'   => '
                    if(confirm(\'Are you sure you want to do this?\')){
                    window.location.href = \''.$this->getUrl('*/video/delete', array(
                            'videoid' => $this->_videoId,
                            'productid' => $this->_productId)).'\';
                    loading();
                    }
                    ',
            ));

            if($this->getRequest()->getParam('uploaded'))
            {
                $this->_addButton('deletevideo', array(
                        'label'     => Mage::helper('adminhtml')->__('Delete Video on Youtube'),
                        'class'     => 'delete',
                        'onclick'   => '
                        if(confirm(\'Are you sure you want to do this?\')){
                        window.location.href = \''.$this->getUrl('*/video/delete', array(
                                'videoid' => $this->_videoId,
                                'productid' => $this->_productId, 'deleteYoutube' => 'true')).'\';
                        loading();
                        }
                        ',
                ));
                $this->removeButton('save');
            }
        }

        $this->_updateButton('save', 'label', $this->__('Save Url'), 'onclick' ,'loading(); editForm.submit();');
        $this->_updateButton('save', 'onclick' ,'loading(); editForm.submit();');
        $this->_updateButton('back', 'onclick', 'loading();setLocation(\'' . $this->getUrl('*/catalog_product/edit/id/'.$this->_productId.'/back/edit/tab/product_info_tabs_videos/') . '\')');
        $this->removeButton('reset');
    }

    public function getHeaderText()
    {
        if (Mage::registry('drecomm_productvideo')->getId())
        {
            return $this->__('Edit Video');
        }
        else
        {
            return $this->__('New Video');
        }
    }
}