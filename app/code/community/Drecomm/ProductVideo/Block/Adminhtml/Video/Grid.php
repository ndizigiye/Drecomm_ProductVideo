<?php
/**
 * Grid class
 * @package    Block
 * @author ndizigiye
 */
class Drecomm_ProductVideo_Block_Adminhtml_Video_Grid
extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * The id of the current product
     * @var string
     */
    private $_productId;

    /**
     * Init class
     * @package Block
     */
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('id');
        $this->setId('drecomm_productvideo_video_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->_productId = $this->getRequest()->getParam('id');
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('video');
        $this->getMassactionBlock()->addItem('delete', array(
                'label'=> 'Delete',
                'url'  => $this->getUrl('*/video/massDelete/productid/'.$this->_productId),
                'confirm' => Mage::helper('tax')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('disable', array(
                'label'=> 'Disable',
                'url'  => $this->getUrl('*/video/massDisable/productid/'.$this->_productId)
        ));

        $this->getMassactionBlock()->addItem('enable', array(
                'label'=> 'Enable',
                'url'  => $this->getUrl('*/video/massEnable/productid/'.$this->_productId)
        ));

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('drecomm_productvideo/video_collection');
        $collection->addFieldToFilter('productid', $this->_productId); // show only video of the loaded product
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     * @package Block
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id',
                array(
                        'header'=> $this->__('ID'),
                        'align' =>'right',
                        'width' => '50px',
                        'index' => 'id'
                )
        );

        $this->addColumn('url',
                array(
                        'header'=> $this->__('URL video'),
                        'index' => 'url'
                )
        );

        $this->addColumn('status',
                array(
                        'header'=> $this->__('Status'),
                        'index' => 'status'
                )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        $url = $this->getUrl('*/video/edit', array('videoid' => $row->getId(),'productid' => $this->_productId));
        if(strstr($row->getUrl(),"~"))
        {
            $url = $this->getUrl('*/video/edit', array('videoid' => $row->getId(),'productid' => $this->_productId,'uploaded' => 'true'));
        }

        return $url;
    }
}
