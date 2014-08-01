<?php

$installer = $this;
$installer->startSetup();

/**
 * Create table 'puntopagos/transaction'
 */
$table = $installer->getConnection()
// The following call to getTable('foo_bar/baz') will lookup the resource for foo_bar (foo_bar_mysql4), and look
// for a corresponding entity called baz. The table name in the XML is foo_bar_baz, so ths is what is created.
    ->newTable($installer->getTable('puntopagos/transaction'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'ID'
    )->addColumn('token', Varien_Db_Ddl_Table::TYPE_VARCHAR, 25, array(
        'nullable'  => false,
    ), 'Punto pagos Token')

    ->addColumn('trx_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 25, array(
        'nullable'  => false,
    ), 'Transaction ID (Order Number in Magento)')

    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15, array(
        'nullable'  => false,
    ), 'Punto pago transaction status')

    ->addColumn('amount', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15, array(
        'nullable'  => false,
    ), 'Transaction amount')

    ->addColumn('start_date', Varien_Db_Ddl_Table::TYPE_DATETIME, 0, array(
        'nullable'  => false,
    ), 'Transaction start date')

    ->addColumn('approval_date', Varien_Db_Ddl_Table::TYPE_DATETIME, 0, array(
        'nullable'  => true,
    ), 'Transaction end date')

    ->addColumn('payment_option', Varien_Db_Ddl_Table::TYPE_INTEGER, 3, array(
        'nullable'  => true,
    ), 'Medio Pago')

    ->addColumn('payment_option_desc', Varien_Db_Ddl_Table::TYPE_VARCHAR, 60, array(
        'nullable'  => true,
    ), 'Descripcion Medio Pago')

    ->addColumn('response', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => true,
    ), 'Response status (Resultado de la transaccion)')

    ->addColumn('auth_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 30, array(
        'nullable'  => true,
    ), 'Codigo de autorizacion')

    ->addColumn('operation_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 30, array(
        'nullable'  => true,
    ), 'NUmero de la operacion');

$installer->getConnection()->createTable($table);
$installer->endSetup();