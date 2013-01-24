<?php
/**
 * install new table in the database
 * @package    sql
 * @author ndizigiye
 *
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'foo_bar_baz'
 */
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('catalog_product_entity_media_video')};
CREATE TABLE IF NOT EXISTS {$this->getTable('catalog_product_entity_media_video')} (
`id` int(11) unsigned NOT NULL auto_increment,
`url` varchar(255) NOT NULL default '',
`path` varchar(255) NOT NULL default '',
PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();