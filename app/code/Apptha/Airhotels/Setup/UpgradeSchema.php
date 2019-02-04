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
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), "1.0.1", "<")) {
        /**
         * Create table 'airhotels_uploadvideo'
         */
        $table = $setup->getConnection()->newTable($setup->getTable('airhotels_uploadvideo'))
->addColumn('id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'airhotels_uploadvideo' )
->addColumn('image_url',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,'64k',[],'image_url')
->addColumn('status',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,['nullable' => false],'status')
->addColumn( 'created_at',\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null,['nullable' => false],'created_at')
->setComment('Apptha Airhotels airhotels_uploadvideo');
        $setup->getConnection()->createTable($table);
        }

         if (version_compare($context->getVersion(), "1.0.2", "<")) {
        /**
         * Create table 'Customer Reply'
         */
        $table = $setup->getConnection()->newTable($setup->getTable('airhotels_customerreply'))
    ->addColumn('id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'ID' )
    ->addColumn('message_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,[],'Message ID')
    ->addColumn('sender_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,['nullable' => false],'Sender ID')
    ->addColumn( 'receiver_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11,['nullable' => false],'Receiver ID')
    ->addColumn('message',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,[],'Message')
    ->addColumn('is_read',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,['default' => '0'],'Read Status')
    ->setComment('Customer Reply table');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'Contact Host'
         */
        $table =$setup->getConnection()->newTable(
                $setup->getTable('airhotels_contacthost'))
        ->addColumn('id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'Id')
        ->addColumn('sender_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,['nullable' => false],'Sender ID')
        ->addColumn( 'receiver_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11,['nullable' => false],'Receiver ID')
        ->addColumn( 'product_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11,['nullable' => false],'Product ID')
         ->addColumn( 'checkin',\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null,['nullable' => false],'Check In')
          ->addColumn( 'checkout',\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null,['nullable' => false],'Check Out')
          ->addColumn('message',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,\Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,[],'Message')
          ->addColumn('email',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,null,['nullable' => false],'Email')
          ->addColumn('phoneno',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,['nullable' => false],'PhoneNumber')
          ->addColumn('guests',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,['nullable' => false],'Guests')
          ->addColumn('sender_read',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,['nullable' => false],'Sender Read')
          ->addColumn('receiver_read',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,['nullable' => false],'Receiver Read')
          ->addColumn('read_flag',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,['nullable' => false],'Read Flag')        
        ->addColumn( 'created_at',\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null,['nullable' => false],'created_at')

        ->addColumn('is_sender_delete',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,['nullable' => false],'Sender Delete status')
        ->addColumn('is_receiver_delete',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,['nullable' => false],'Receiver Delete status')
        ->addColumn('is_admin_delete',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,['nullable' => false],'admin Delete status')
        ->addColumn('reply_message_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,['nullable' => false],'reply_message_id')
        ->addColumn('sent_message_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,11,['nullable' => false],'sent_message_id')
        ->setComment('Contact Host table');
        $setup->getConnection()->createTable($table);


        /**
         * Create table 'customer_login_status'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('customer_login_status')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Customer Id'
        )->addColumn(
            'firstname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'First Name'
        )->addColumn(
            'lastname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Last Name'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Email'
        )->addColumn(
            'login_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true, 'default' => 'nullable'],
            'Login Time'
        )->addColumn(
            'logout_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true, 'default' => 'nullable'],
            'Logout Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'Is Active'
        )
        ->setComment('Customer login status');
        $setup->getConnection()->createTable($table);
        }
        $setup->endSetup();
    }
}