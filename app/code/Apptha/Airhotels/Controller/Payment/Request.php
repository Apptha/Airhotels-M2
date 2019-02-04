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
namespace Apptha\Airhotels\Controller\Payment;

/**
 * This class contains the host payment transaction
 */
class Request extends \Magento\Framework\App\Action\Action {
    /**
     * Function to send payment request
     *
     * @return $array
     */
    public function execute() {

        /**
         * Check whether module enabled or not
         */
            $logedInUser = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );

            if ($logedInUser->isLoggedIn ()) {
                $transactionId = $this->getRequest ()->getParam ( 'id' );
                if (! empty ( $transactionId )) {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
                    $hostOrder = $objectManager->get ( 'Apptha\Airhotels\Model\Hostorder' )->load($transactionId);
                    $hostOrder->setPaymentStatus(1);
                    $hostOrder->save();
                    $this->sendPaymentRequestEmail($hostOrder);
                    $this->_redirect ( 'booking/listing/transactions/' );
                }
            } else {
                    $this->_redirect ( 'customer/account' );
            }
    }
    /**
     * Function to send payment request email to admin
     * @param object $hostOrder
     * 
     * @return void
     */
    public function sendPaymentRequestEmail($hostOrder){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // loading host details
        $host = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getHostId());
        /**
         * Property Email Owner
         */
        $recipient = $host->getEmail();
        /**
         * Property Email Owner
         */
        $hostName = $host->getName();
        $templateId = 'airhotels_host_payment_request';
        /* Sender Detail */
        $senderInfo = [
            'name' => $hostName,
            'email' => $recipient
        ];
        $admin = $objectManager->get('Apptha\Airhotels\Helper\Data');
        /**
         * Assign admin details
         */
        $adminName = $admin->getAdminName();
        $adminEmail = $admin->getAdminEmail();
        /* Receiver Detail */
        $receiverInfo = [
            'name' => $adminName,
            'email' => $adminEmail
        ];
        /* Template variables Detail */
        $emailTempVariables = (array(
            'name' => $adminName,
            'order_id' => $hostOrder->getOrderId()
        ));
        /* call send mail method from helper or where you define it */
        $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId);
    }
}
