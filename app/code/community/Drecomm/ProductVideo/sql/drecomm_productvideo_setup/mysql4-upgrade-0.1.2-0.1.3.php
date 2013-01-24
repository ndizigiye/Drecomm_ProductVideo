<?php
/**
 * delete column 'path' if it exists and add new column 'status'
 * the status of a video can be 'enabled' or 'disabled'.
 * @package    sql
 * @author ndizigiye
 */

/**
 * Installer object
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$videoTable = $installer->getTable('catalog_product_entity_media_video');

if ($installer->getConnection()->tableColumnExists($videoTable, 'path'))
{
    $installer->getConnection()->dropColumn($videoTable, 'path');
}

$installer->getConnection()->addColumn(
        $videoTable,
        'status',
        'varchar (8) NOT NULL DEFAULT "enabled" '
);
$installer->endSetup();