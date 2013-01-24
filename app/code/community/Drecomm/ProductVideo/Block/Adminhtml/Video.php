<?php
/**
 * Grid container
 * @package    Block
 * @author ndizigiye
 *
 */
class Drecomm_ProductVideo_Block_Adminhtml_Video
extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $productId = $this->getRequest()->getParam('id');
        $this->_blockGroup = 'drecomm_productvideo';
        $this->_controller = 'adminhtml_video';
        $this->_headerText = $this->__('Videos');
        $this->_updateButton('add', 'label', $this->__('Add New Video Url'),'onclick', 'setLocation(\'' . $this->getCreateUrl() .'\')');
        $this->_addButton('upload', array(
                'label'     => 'Upload Video',
                'onclick'   => 'loading();setLocation(\'' . $this->getUrl('*/video/new/productid/'.$productId.'/upload/true') .'\')',
                'class'     => 'add',
        ));
        $this->_updateButton('add', 'onclick', 'loading();setLocation(\'' . $this->getUrl('*/video/new/productid/'.$productId.'') . '\')');
    }
}