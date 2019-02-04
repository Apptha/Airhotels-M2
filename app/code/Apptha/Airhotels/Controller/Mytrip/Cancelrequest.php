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
 * Clas contains cancel request for order
 * *
 */
namespace Apptha\Airhotels\Controller\Mytrip;

class Cancelrequest extends \Magento\Framework\App\Action\Action
{

    /**
     * Handling cancel request
     * *
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $hostOrderModel = $objectManager->get('Apptha\Airhotels\Model\Hostorder')->load($orderId, 'order_item_id');
        $hostOrderModel->setCancelRequestStatus(1);
        $hostOrderModel->save();
        $this->sendCancelRequestEmail($hostOrderModel);
        $this->messageManager->addSuccess(__('Booking cancellation request has been sent successfully.'));
        $this->_redirect('airhotels/mytrip/upcomingtrip');
    }
    /**
     * Handling cancel request email
     * Apptha\Airhotels\Model\Hostorder $hostOrder
     * 
     * return void
     **/
    public function sendCancelRequestEmail($hostOrder){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // loading host details
        $host = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getHostId());
        /**
         * Property Email Owner
         */
        $recipient = $host->getEmail();
        $customer = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getCustomerId());
        $customerName=$customer->getName();
        $customerEmail=$customer->getEmail();
        /**
         * Property Email Owner
         */
        $hostName = $host->getName();
        $admin = $objectManager->get('Apptha\Airhotels\Helper\Data');
        /**
         * Assign admin details
         */
        $adminEmail = $admin->getAdminEmail();
        $templateId = 'airhotels_order_item_request_template';
        /* Sender Detail */
        $senderInfo = [
            'name' => $customerName,
            'email' =>$customerEmail
        ];
        /* Receiver Detail */
        $receiverInfo = [
            'name' => $hostName,
            'email' => $recipient
        ];
        /* Template variables Detail */
        $emailTempVariables = (array(
            'name' => $hostName,
            'cname' => $customerName,
            'cemail' => $customerEmail,
            'order_id' => $hostOrder->getOrderId()
        ));
        /* call send mail method from helper or where you define it */
        $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId, $adminEmail);
    }
}
