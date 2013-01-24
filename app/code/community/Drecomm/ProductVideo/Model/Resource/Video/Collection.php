<?php
/**
 * Collection class extends from parent
 * @package    Model
 * @author ndizigiye
 *
 */
class Drecomm_ProductVideo_Model_Resource_Video_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('drecomm_productvideo/video');
    }
}