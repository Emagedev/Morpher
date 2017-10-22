<?php
/**
 * Emagedev extension for Magento
 * 
 * Add table for custom menu
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the VF CustomMenu module to newer versions in the future.
 * If you wish to customize the VF CustomMenu module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Emagedev
 * @package    Emagedev_RussianLanguage
 * @copyright  Copyright (C) 2012 Vladimir Fishchenko (http://fishchenko.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @var $this Mage_Core_Model_Resource_Setup
 */

$installer = $this;
$installer->startSetup();

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$table = $installer->getConnection()
    ->newTable($installer->getTable('emagedev_russian/inflection'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn('phrase', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'Phrase to Inflect')
    ->addColumn('inflection', Varien_Db_Ddl_Table::TYPE_VARCHAR, 5, array(
        'default'   => 0,
        'nullable'  => false,
    ), 'Inflection Code')
    ->addColumn('multi', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Is Multiple Form')
    ->addColumn('inflected_phrase', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'Result');
$installer->getConnection()->createTable($table);

$installer->endSetup();