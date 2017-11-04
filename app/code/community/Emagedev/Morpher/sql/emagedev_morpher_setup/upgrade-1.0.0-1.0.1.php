<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

$connection = $installer->getConnection();

$tableName = $installer->getTable('morpher/inflection');

$indexName = $installer->getIdxName(
    'morpher/inflection',
    array(
        'phrase',
        'inflection',
        'multi',
        'flags',
    ),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

// Magento's PDO cannot create INDEX on text field because cannot define key length
// See MySQL error #1170 - BLOB/TEXT column 'name' used in key specification without a key length
$sql = 'ALTER TABLE ' . $installer->getTable('morpher/inflection') . ' ADD INDEX ' . $indexName . '(phrase(255), inflection, multi, flags);';

$installer->run($sql);

$installer->endSetup(); 