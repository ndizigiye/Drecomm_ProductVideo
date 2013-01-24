<?php
/**
 * Resource class extends from parent
 * @package    Model
 * @author ndizigiye
 *
 */
class Drecomm_ProductVideo_Model_Resource_Video extends Mage_Core_Model_Resource_Db_Abstract
{
protected function _construct()
{
$this->_init('drecomm_productvideo/video', 'id');
}
}