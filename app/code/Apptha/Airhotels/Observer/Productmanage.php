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
 * This class contains product details functions
 */
class Productmanage implements ObserverInterface
{   
    /**
     * Sending email to customer for admin approval/disapproval
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $_product = $observer->getProduct(); // you will get product object
        $propertyApproved= $_product->getPropertyApproved();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $property = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $_product->getId() );
        $propertyUnapproval=$property->getPropertyApproved ();
        $propertyName = $property->getName ();
        $productUrl = $property->getProductUrl ();
        $userId = $property->getUserId ();
        $customer = $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $userId );
        $recipient = $customer->getEmail ();
        $customerName = $customer->getName ();
        $admin = $objectManager->get ( 'Apptha\Airhotels\Helper\Data' );
            /**
             * Assign admin details
             */
        $adminName = $admin->getAdminName ();
        $adminEmail = $admin->getAdminEmail ();
        /* Here we prepare data for our email  */
        if ($userId && $propertyApproved && empty ( $propertyUnapproval )) {
             /* Sender Detail  */
                $senderInfo = [
                    'name' => $adminName,
                    'email' => $adminEmail,
                ];
                /**
                 * Getting the Property templeId value
                 */
                
                /* Receiver Detail  */
                $receiverInfo = [
                    'name' =>$customerName,
                    'email' =>$recipient
                ];
                $templateId = 'airhotels_product_approval_template';
                $emailTempVariables = (array (
                    'ownername' => $adminName,
                    'pname' => $propertyName,
                    'purl' => $productUrl,
                    'cname' => $customerName
                ));
                $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod(
                    $emailTempVariables,
                    $senderInfo,
                    $receiverInfo,
                    $templateId
                    );
            }
        
            /**
             * email template for disapproved property
             */
            if (   $userId && empty($propertyApproved) && $propertyUnapproval) {
                /* Receiver Detail  */
                $receiverInfo = [
                    'name' => $customerName,
                    'email' =>$recipient,
                ];
                
                
                /* Sender Detail  */
                $senderInfo = [
                    'name' => $adminName,
                    'email' => $adminEmail,
                ];
                $templateId = 'airhotels_product_disapproval_template';
                $emailTempVariables = (array (
                    'ownername' =>$adminName,
                    'pname' => $propertyName,
                    'purl' => $productUrl,
                    'cname' => $customerName
                ));
                /* We write send mail function in helper because if we want to
                 use same in other action then we can call it directly from helper */
                
                /* call send mail method from helper or where you define it*/
                $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod(
                    $emailTempVariables,
                    $senderInfo,
                    $receiverInfo,
                    $templateId
                );
            }
        

    }
}