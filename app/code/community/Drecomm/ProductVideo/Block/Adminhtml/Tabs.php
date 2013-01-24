<?php
/**
 * Tabs class
 * @package    Block
 * @author ndizigiye
 *
 */
class Drecomm_ProductVideo_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    /**
     *the parent object variable
     * @var parent class object
     */
    private $parent;

     /**
      * @return void
      *add a new tab in de backend
      */
     protected function _prepareLayout()
     {
         //get all existing tabs
         $this->parent = parent::_prepareLayout();
        //add new tab
         if ($this->getRequest()->getParam('id')){
        $this->addTab('videos', array(
                'label'     => Mage::helper('catalog')->__('Videos'),
                'content'   => $this->_translateHtml($this->getLayout()
                              ->createBlock('drecomm_productvideo/adminhtml_video')->toHtml()),
        ));
         }
         return $this->parent;
      }
}