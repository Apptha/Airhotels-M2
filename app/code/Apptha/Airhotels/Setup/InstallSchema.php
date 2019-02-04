<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Airhotels
 * @version     1.0.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2017 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
namespace Apptha\Airhotels\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
class InstallSchema implements InstallSchemaInterface {
    /**
     * (non-PHPdoc)
     *
     * @see \Apptha\Airhotels\Setup\InstallSchemaInterface::install()
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();
        /**
         * create the customer profile table
         * @var \Magento\Framework\DB\Ddl\Table $table
         */
        $table =$setup->getConnection()->newTable(
                $setup->getTable('airhotels_customer_profile'))
                ->addColumn('id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'Id')
                        ->addColumn('customer_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,[],'Customer Id')
                        ->addColumn('gender',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,10,[],'Gender')
                        ->addColumn('dob',\Magento\Framework\DB\Ddl\Table::TYPE_DATE,'',[],'DOB')
                        ->addColumn('phone',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,[],'Phone')
                        ->addColumn('description',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,[],'Describe yourself')
                        ->addColumn('city',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,[],'City')
                        ->addColumn('country',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,[],'Country')
                        ->addColumn('paypal',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,[],'Paypal Id')
                        ->addColumn('bankdetails',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,[],'Bank details')
                        ->addColumn('profileimage',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,[],'profileimage')
                        ->setComment('Custom Table');
        $setup->getConnection()->createTable($table);
        /**
         * profile city table
         */
        $table =$setup->getConnection()->newTable(
                $setup->getTable('airhotels_city'))
                ->addColumn('id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'Id')
                        ->addColumn('name',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,[],'name')
                        ->addColumn('description',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,[],'Description')
                        ->addColumn('images',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,[],'Images')
                        ->addColumn('status',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,1,[],'Status')
                        ->setComment('Manage City Table');
                        $setup->getConnection()->createTable($table);
        /**
         * verify host table
         */
       $table =$setup->getConnection()->newTable(
            $setup->getTable('airhotels_verify_host'))
            ->addColumn('id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,
                          ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'Id')
                          ->addColumn('tag_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,[],'Tag Id')
                          ->addColumn('host_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,[],'Host Id')
                          ->addColumn('host_name',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,[],'Host Name')
                          ->addColumn('host_email',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,[],'Host Email')
                          ->addColumn('file_path',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,[],'File Path')
                          ->addColumn('host_tags',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,[],'Host Tags')
                          ->addColumn('country_code',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,[],'Country Code')
                          ->addColumn('id_type',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,[],'Id Type')
                          ->setComment('Verify Host Table');
                          $setup->getConnection()->createTable($table);

                          $table =$setup->getConnection()->newTable(
                                  $setup->getTable('airhotels_calendar'))
                                  ->addColumn('id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,
                                          ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'Id')
                                          ->addColumn('product_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,[],'Product Id')
                                          ->addColumn('book_avail',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,[],'Booking Availability')
                                          ->addColumn('month',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,[],'Month')
                                          ->addColumn('year',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,[],'Year')
                                          ->addColumn('blockfrom',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,[],'Block From')
                                          ->addColumn('price',\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,'10,0',[],'Price')
                                          ->addColumn('created',\Magento\Framework\DB\Ddl\Table::TYPE_DATE,null,[],'Created')
                                          ->addColumn('updated',\Magento\Framework\DB\Ddl\Table::TYPE_DATE,null,[],'Updated')
                                          ->setComment('Airhotels Calendar');
                                          $setup->getConnection()->createTable($table);
                                          /**
                                           * profile city table
                                           */
                      $table =$setup->getConnection()->newTable(
                              $setup->getTable('airhotels_property'))
                              ->addColumn('id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,
                                      ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'Id')
                                      ->addColumn('entity_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,100,[],'Entity Id')
                                      ->addColumn('customer_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,100,[],'Customer Id')
                                      ->addColumn('fromdate',\Magento\Framework\DB\Ddl\Table::TYPE_DATE,null,[],'Fromdate')
                                      ->addColumn('todate',\Magento\Framework\DB\Ddl\Table::TYPE_DATE,null,[],'Todate')
                                      ->addColumn('accomodates',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,100,[],'Accomodates')
                                      ->addColumn('host_fee',\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,'11,2',[],'Host Fee')
                                      ->addColumn('service_fee',\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,'11,2',[],'Service Fee')
                                      ->addColumn('subtotal',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,[],'subtotal')
                                      ->addColumn('order_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,100,[],'Order Id')
                                      ->addColumn('order_item_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,100,[],'Order Item Id')
                                      ->addColumn('order_status',\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,6,[],'Order Status')
                                      ->addColumn('status',\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,6,[],'Status')
                                      ->addColumn('product_name',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,500,[],'Product Name')
                                      ->addColumn('customer_email',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,[],'Customer Email')
                                      ->addColumn('base_currency_code',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,25,[],'Base Currency Code')
                                      ->addColumn('order_currency_code',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,25,[],'Order Currency Code')
                                      ->addColumn('message',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,500,[],'Message')
                                      ->addColumn('cancel_order_status',\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,6,[],'Cancel Order Status')
                                      ->addColumn('cancel_request_status',\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,6,[],'Cancel Request Status')
                                      ->addColumn('grand_total',\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,'12,2',[],'Grand Total')
                                      ->addColumn('payment_request_status',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,2,[],'Payment Request Status')
                                      ->addColumn('host_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,100,[],'Host Id')
                                      ->setComment('Airhotels Property');
                                      $setup->getConnection()->createTable($table);
                                      $setup->getConnection()
                                      ->addColumn($setup->getTable('airhotels_city'),'status', array(
                                              'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                              'nullable'  => false,
                                              'length'    => 1,
                                              'comment'   => 'Status'
                                      ));


                                      $setup->getConnection()
                                      ->addColumn($setup->getTable('sales_order'),'fee_amount', array(
                                              'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                                              'nullable'  => false,
                                              'length'    => '10,2',
                                              'comment'   => 'Status'
                                      ));

                                      $setup->getConnection()
                                      ->addColumn($setup->getTable('sales_order'),'base_fee_amount', array(
                                              'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                                              'nullable'  => false,
                                              'length'    => '10,2',
                                              'comment'   => 'Status'
                                      ));


                                      $setup->getConnection()
                                      ->addColumn($setup->getTable('quote_address'),'fee_amount', array(
                                              'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                                              'nullable'  => false,
                                              'length'    => '10,2',
                                              'comment'   => 'Status'
                                      ));

                                      $setup->getConnection()
                                      ->addColumn($setup->getTable('quote_address'),'base_fee_amount', array(
                                              'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                                              'nullable'  => false,
                                              'length'    => '10,2',
                                              'comment'   => 'Status'
                                      ));

                  $hostOrderTableName =  'airhotels_hostorder';
                      $hostOrderTable = $setup->getConnection ()->newTable ( $hostOrderTableName )
                      ->addColumn ( 'id', Table::TYPE_INTEGER, null, ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true], 'Id' )
                      ->addColumn ( 'order_item_id', Table::TYPE_INTEGER, null, [ 'unsigned' => true ], 'Order Item Id' )
                      ->addColumn ( 'listing_name', Table::TYPE_TEXT, 255, [ 'unsigned' => true ], 'Listing Name' )

                      ->addColumn ( 'host_id', Table::TYPE_INTEGER, null, ['unsigned' => true ], 'Host Id' )
                      ->addColumn ( 'host_product_total', Table::TYPE_DECIMAL, '12,4', [ ], 'Host Listing Total' )
                      ->addColumn ( 'host_amount', Table::TYPE_DECIMAL, '12,4', [ ], 'Host Amount' )
                      ->addColumn ( 'is_invoiced', Table::TYPE_SMALLINT, null, ['unsigned' => true ], 'Is Invoiced' )
                      ->addColumn ( 'is_canceled', Table::TYPE_SMALLINT, null, ['unsigned' => true ], 'Is Canceled' )
                      ->addColumn ( 'order_status', Table::TYPE_TEXT, 255, [ ], 'Order Status' )
                      ->addColumn ( 'order_id', Table::TYPE_TEXT, 255, [ ], 'Order Id' )
                      ->addColumn ( 'billing_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Billing Id' )
                      ->addColumn ( 'quote_id', Table::TYPE_INTEGER, null, [ 'unsigned' => true ], 'Quote Id' )
                      ->addColumn ( 'order_currency_code', Table::TYPE_TEXT, 3, [ ], 'Currency Code' )
                      ->addColumn ( 'customer_id', Table::TYPE_INTEGER, null, [ 'unsigned' => true], 'Customer Id' )
                      ->addColumn ( 'fromdate', Table::TYPE_DATE, null, [ 'unsigned' => true], 'Fromdate' )
                      ->addColumn ( 'todate', Table::TYPE_DATE, null, [ 'unsigned' => true], 'Todate' )
                      ->addColumn ( 'accomodates', Table::TYPE_INTEGER, 11, [ 'unsigned' => true], 'Accomodates' )
                      ->addColumn ( 'commission_fee', Table::TYPE_DECIMAL, '11,2', [ 'unsigned' => true], 'Commission Fee' )
                      ->addColumn ( 'service_fee', Table::TYPE_DECIMAL, '11,2', [ 'unsigned' => true], 'Service Fee' )
                      ->addColumn ('payment_status',Table::TYPE_INTEGER, 2, [], '1-host request,2-Not paid To Hoster,3-Refund To Guest,4-Paid To Hoster,5-Acknowledgement Sent')
                      ->addColumn ('payment_comment',Table::TYPE_TEXT,500, [], 'Admin comment while update the host order payment status')
                      ->addColumn ( 'entity_id', Table::TYPE_INTEGER, 11, [ 'unsigned' => true], 'Entity Id' )
                      ->setComment ( 'Host Order Table' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
                      $setup->getConnection ()->createTable ( $hostOrderTable );
                  $hostOrderItemsTableName = 'airhotels_hostorderitems';
                      $hostOrderItemTable = $setup->getConnection ()->newTable ( $hostOrderItemsTableName )
                      ->addColumn ( 'id', Table::TYPE_INTEGER, null, ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true], 'Id' )
                      ->addColumn ( 'order_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Order Id' )->addColumn ( 'host_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Host Id' )
                      ->addColumn ( 'order_item_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Order Item Id' )->addColumn ( 'product_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Product Id' )
                      ->addColumn ( 'product_sku', Table::TYPE_TEXT, 255, [ ], 'Product Sku' )->addColumn ( 'product_qty', Table::TYPE_DECIMAL, '12,4', [ ], 'Product Qty' )
                      ->addColumn ( 'product_name', Table::TYPE_TEXT, 255, [ ], 'Product Name' )->addColumn ( 'options', Table::TYPE_TEXT, null, [ ], 'Options' )
                      ->addColumn ( 'is_canceled', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Canceled' )->addColumn ( 'status', Table::TYPE_TEXT, 255, [ ], 'Status' )
                      ->addColumn ( 'parent_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Parent Id' )->addColumn ( 'quote_item_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Quote Item Id' )
                      ->addColumn ( 'quote_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Quote Id' )->addColumn ( 'created_at', Table::TYPE_DATETIME, null, ['nullable' => false], 'Created At' )
                      ->addColumn ( 'qty_canceled', Table::TYPE_DECIMAL, '12,4', [ ], 'Qty Canceled' )->addColumn ( 'commission', Table::TYPE_DECIMAL, '12,4', [ ], 'Commission' )
                      ->addColumn ( 'product_price', Table::TYPE_DECIMAL, '12,4', [ ], 'Product Price' )->addColumn ( 'base_product_price', Table::TYPE_DECIMAL, '12,4', [ ], 'Base Product Price' )
                      ->addColumn ( 'is_buyer_canceled', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Buyer Canceled' )->addColumn ( 'is_buyer_refunded', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Buyer Refunded' )
                      ->addColumn ( 'is_buyer_returned', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Buyer Returned' )->addColumn ( 'is_refunded', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Refunded' )
                      ->addColumn ( 'is_returned', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Returned' )
                      ->setComment ( 'Host Order Items Table' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
                      $setup->getConnection ()->createTable ( $hostOrderItemTable );

                      $hostLongitudeTableName = 'airhotels_longitude';
                          $hostLongitudeTable = $setup->getConnection ()->newTable ( $hostLongitudeTableName )->addColumn ( 'id', Table::TYPE_INTEGER, null, [
                                  'identity' => true,'unsigned' => true,'nullable' => false,'primary' => true
                          ], 'ID' )->addColumn ( 'entity_id', Table::TYPE_INTEGER, 11, ['nullable' => false ], 'Entity Id' )
                          ->addColumn ( 'latitude', Table::TYPE_TEXT, 255, [], 'Latitude' )->addColumn ( 'longitude', Table::TYPE_TEXT, 255, [ ], 'Longitude' )->setComment ( 'Host Longitude Table' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
                          $setup->getConnection ()->createTable ( $hostLongitudeTable );

                          $table = $setup->getConnection()->newTable($setup->getTable('airhotels_uploadvideo'))
                          ->addColumn('id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'airhotels_uploadvideo' )
                          ->addColumn('image_url',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,'64k',[],'image_url')
                          ->addColumn('status',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,['nullable' => false],'status')
                          ->addColumn( 'created_at',\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null,['nullable' => false],'created_at')
                          ->setComment('Apptha Airhotels airhotels_uploadvideo');
                          $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}