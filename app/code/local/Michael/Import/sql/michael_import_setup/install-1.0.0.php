<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Michael
 * @package   Michael_Import
 * @author    Michael Talashov <michael.talashov@gmail.com>
 * @copyright 2018 Michael Talashov
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$tableName = $installer->getTable('michael_import/booking');
if (!$installer->getConnection()->isTableExists($tableName)) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('michael_import/booking'))
        ->addColumn('booking_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ])
        ->addColumn('booking_key', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, ['nullable'  => false])
        ->addColumn('order_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0'
        ])
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0'
        ])
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0'
        ])
        ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0'
        ])
        ->addColumn('product_supplier_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0'
        ])
        ->addColumn('event_date_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, ['nullable'  => false])
        ->addColumn('date_applied_for', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, ['nullable'  => false])
        ->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, ['nullable'  => false])
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, ['nullable'  => false])
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, ['nullable'  => false])
        ->addColumn('ticket_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, ['nullable'  => false])
        ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, ['nullable'  => false])
        ->addColumn('option_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, ['nullable'  => false])
        ->addColumn('variation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,  [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0'
        ])
        ->addColumn('variation_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, ['nullable'  => false])
        ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0'
        ])
        ->addColumn('qty_cancelled', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0'
        ])
        ->addColumn('external_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0'
        ])
        ->addColumn('first_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, ['nullable'  => false])
        ->addColumn('last_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, ['nullable'  => false])
        ->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, ['nullable'  => false])
        ->addColumn('phone_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, ['nullable'  => false])
        ->addColumn('duration_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, ['nullable'  => false])
        ->addColumn('duration_value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', ['nullable'  => false])
        ->addColumn('contact_firstname', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, ['nullable'  => false])
        ->addColumn('contact_lastname', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, ['nullable'  => false])
        ->addColumn('contact_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, ['nullable'  => false])
        ->addColumn('contact_telephone', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, ['nullable'  => false]);

    $table->addIndex($installer->getIdxName('michael_import/booking', ['booking_key']), ['booking_key'])
        ->addIndex($installer->getIdxName('michael_import/booking', ['order_id']), ['order_id'])
        ->addIndex($installer->getIdxName('michael_import/booking', ['product_id']), ['product_id']);

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();