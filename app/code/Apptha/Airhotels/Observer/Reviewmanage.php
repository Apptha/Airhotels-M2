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

/**
 * This class contains to review submit and approve emails
 */
class Reviewmanage implements ObserverInterface
{

    protected $customerSession;

    /**
     *
     * @param Data $airhotelsData
     */
    public function __construct(\Magento\Customer\Model\Session $customerSession)
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->customerSession = $customerSession;
    }

    /**
     * Sending email to host,customer and admin  for review approval
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $observer->getEvent()->getObject()->getData();
        $property = $this->objectManager->get('Magento\Catalog\Model\Product')->load($data['entity_pk_value']);
        $propertyUserId = $property->getUserId();
        $propertyUrl = $property->getProductUrl();
        //Getting customer session details
        $currentCustomer = $this->customerSession->getCustomer();
        $customerName = $currentCustomer->getName();
        $customerEmail = $currentCustomer->getEmail();
        if ($propertyUserId) {
            //loading customer details
            $host = $this->objectManager->get('Magento\Customer\Model\Customer')->load($propertyUserId);
            /**
             * Property Email Owner
             */
            $recipient = $host->getEmail();
            /**
             * Property Email Owner
             */
            $hostName = $host->getName();
            $customer = $this->objectManager->get('Magento\Customer\Model\Customer')->load($data['customer_id']);
            $admin = $this->objectManager->get('Apptha\Airhotels\Helper\Data');
            /**
             * Assign admin details
             */
            $adminName = $admin->getAdminName();
            $adminEmail = $admin->getAdminEmail();
            $storeName =$this->objectManager->get('Apptha\Airhotels\Helper\Order')->getStoreName();
            if ($data['status_id'] == 1) {
                $templateId = 'airhotels_customer_review_email_adminapproval_template';
                /* Sender Detail */
                $senderInfo = [
                    'name' => $adminName,
                    'email' => $adminEmail
                ];
                /* Receiver Detail */
                $receiverInfo1 = [
                    'name' => $hostName,
                    'email' => $recipient
                ];
                $receiverInfo2 = [
                    'name' => $customer->getName(),
                    'email' => $customer->getEmail()
                ];
                /* Template variables Detail */
                $emailTempVariables1 = (array(
                    'store_name'=>$storeName,
                    'cname' => $hostName,
                    'property_name' => $property->getName(),
                    'product_url'=>$propertyUrl,
                    'buyer_name' => $data['nickname'],
                    'review_details' => $data['detail'],
                    'review_status' => 'Approved',
                    'title'=>'Your guest review has been approved by '
                ));
                $emailTempVariables2 = (array(
                    'store_name'=>$storeName,
                    'cname' => $customer->getName(),
                    'property_name' => $property->getName(),
                    'product_url'=>$property->getProductUrl(),
                    'buyer_name' => $data['nickname'],
                    'review_details' => $data['detail'],
                    'review_status' => 'Approved',
                    'title'=>'Your review has been approved by '
                ));
                /*
                 * We write send mail function in helper because if we want to
                 * use same in other action then we can call it directly from helper
                 */
                
                /* call send mail method from helper or where you define it */
                $this->objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables1, $senderInfo, $receiverInfo1, $templateId);
                $this->objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables2, $senderInfo, $receiverInfo2, $templateId);
            } elseif($data['status_id'] == 2 && $customerEmail ) {
                $templateId = 'airhotels_customer_review_for_approval';
                /* Receiver Detail */
                $receiverInfo1 = [
                    'name' => $hostName,
                    'email' => $recipient
                ];
               
                $receiverInfo2 = [
                    'name' => $adminName,
                    'email' => $adminEmail
                ];
                /* Sender Detail */
                $senderInfo = [
                    'name' => $customerName,
                    'email' => $customerEmail
                ];
                /* Template variables Detail */
                $emailTempVariables1 = (array(
                    'name' => $hostName,
                    'cname' => $customerName,
                    'property_name' => $property->getName(),
                    'product_url'=>$property->getProductUrl(),
                    'buyer_name' => $data['nickname'],
                    'review_details' => $data['detail'],
                    'review_status' => 'Pending'
                ));
                $emailTempVariables2 = (array(
                    'name' => $adminName,
                    'cname' => $customerName,
                    'property_name' => $property->getName(),
                    'product_url'=>$property->getProductUrl(),
                    'buyer_name' => $data['nickname'],
                    'review_details' => $data['detail'],
                    'review_status' => 'Pending'
                ));
                
                /* sending mail to host*/
                $this->objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables1, $senderInfo, $receiverInfo1, $templateId);
                /* sending mail to admin*/
                $this->objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables2, $senderInfo, $receiverInfo2, $templateId);
            }
        }
    }
}