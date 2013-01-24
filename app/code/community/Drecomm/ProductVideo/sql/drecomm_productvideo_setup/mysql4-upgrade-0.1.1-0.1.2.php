<?php
/**
 * install new column on the video table in the database
 * @package    sql
 * @author ndizigiye
 *
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
        $this->getTable('catalog_product_entity_media_video'), //table name
        'productid',      //column name
        'int(11) NOT NULL DEFAULT 0'  //datatype definition
);

$installer->endSetup();