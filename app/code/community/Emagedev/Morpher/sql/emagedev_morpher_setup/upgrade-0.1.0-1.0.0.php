<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

$connection = $installer->getConnection();

$installer->getConnection()
    ->addColumn($installer->getTable('morpher/inflection'),'flags', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => false,
        'length'    => 255,
        'after'     => null,
        'comment'   => 'Serialized flags'
    ));

$installer->endSetup(); 