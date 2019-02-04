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
 */
/**
 * Class contains cancel order
 * *
 */
namespace Apptha\Airhotels\Controller\Mytrip;
use Magento\Framework\Controller\ResultFactory;

class Cancel extends \Magento\Framework\App\Action\Action
{

    /**
     * cancel the order
     * *
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->get('\Magento\Sales\Model\Order')->load($orderId);
        $order->cancel();
        $order->save();
        /**
         * getting invoice incremental id
         */
        $invIncrementIDs = array();
        foreach ($order->getInvoiceCollection() as $invoice) {
            $invIncrementIDs[] = $invoice->getIncrementId();
        }
        if (empty($invIncrementIDs)) {
            $hostOrderModel = $objectManager->get('Apptha\Airhotels\Model\Hostorder')->load($orderId, 'order_item_id');
            $hostOrderModel->setOrderStatus('cancelled');
            $hostOrderModel->setCancelRequestStatus(2);
            $hostOrderModel->save();
        } else {
            $hostOrderModel = $objectManager->get('Apptha\Airhotels\Model\Hostorder')->load($orderId, 'order_item_id');
            $hostOrderModel->setOrderStatus('cancelled');
            $hostOrderModel->setCancelRequestStatus(3);
            $hostOrderModel->save();
        }
        $this->sendOrderStatusEmail($order);
        $this->messageManager->addSuccess ( __ ( 'Booking cancelled successfully.' ) );
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
     /**
     * Sending order status email to customer
     * 
     * @param Order $order
     * @return void
     */
    public function sendOrderStatusEmail($order ){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * Load hostorder data by order id
         */
        $hostOrder = $objectManager->get('Apptha\Airhotels\Model\Hostorder')->load($order->getId(), 'order_item_id');
        // loading customer details
        $host = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getHostId());
        /**
         * Property Email Owner
         */
        $recipient = $host->getEmail();
        $customer = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getCustomerId());
        $customerName = $customer->getName();
        $customerEmail = $customer->getEmail();
         $admin = $objectManager->get('Apptha\Airhotels\Helper\Data');
        /**
         * Assign admin details
         */
        $adminName = $admin->getAdminName();
        $adminEmail = $admin->getAdminEmail();
        $templateId = 'airhotels_order_status';
        /**
         * mail sender name
         */
        /* Receiver Detail */
        $receiverInfo = [
            'name' => $customerName,
            'email' => $customerEmail
        ];
        /* Sender Detail */
        $senderInfo = [
            'name' => $adminName,
            'email' => $adminEmail
        ];
        
        /* Template variables Detail */
        $emailTempVariables = (array(
            'customername' => $customerName,
            'orderstatus' => $order->getStatus(),
            'incrementid' => $order->getIncrementId(),
        ));
        /*
         * We write send mail function in helper because if we want to
         * use same in other action then we can call it directly from helper
         */
        
        /* call send mail method from helper or where you define it */
        $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId, $recipient);
    }
}
