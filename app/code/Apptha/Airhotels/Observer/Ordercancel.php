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
 * @version     1.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2017 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
namespace Apptha\Airhotels\Observer;

use Magento\Framework\Event\ObserverInterface;
use Apptha\Airhotels\Helper\Data;

/**
 * This class contains saving order details functions
 */
class Ordercancel implements ObserverInterface
{

    /**
     * Execute the result
     *
     * @see \Magento\Framework\Event\ObserverInterface::execute()
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        /**
         * Create object instance
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * Load host order data by order id
         */
        $hostOrder = $objectManager->get('Apptha\Airhotels\Model\Hostorder')->load($order->getId(), 'order_item_id');
        // loading host details
        $host = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getHostId());
        /**
         * Property Email Owner
         */
        $hostEmail = $host->getEmail();
        /**
         * Property Email Owner
         */
        $hostName = $host->getName();
        // loading customer details
        $customer = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getCustomerId());
        $customerName=$customer->getName();
        $customerEmail=$customer->getEmail();
        $admin = $objectManager->get('Apptha\Airhotels\Helper\Data');
        /**
         * Assign admin details
         */
        $adminEmail = $admin->getAdminEmail();
        $templateId = 'airhotels_order_item_cancel_return_template';
        /* Sender Detail */
        $senderInfo = [
            'name' => $hostName,
            'email' => $hostEmail
        ];
        /* Receiver Detail */
        $receiverInfo = [
            'name' => $customerName,
            'email' => $customerEmail
        ];
        /* Template variables Detail */
        $emailTempVariables = (array(
            'cname' => $customerName,
            'order_id' => $hostOrder->getOrderId()
        ));
        
        /*
         * We write send mail function in helper because if we want to
         * use same in other action then we can call it directly from helper
         */
        
        /* call send mail method from helper or where you define it */
        $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId,$adminEmail);
    }
}