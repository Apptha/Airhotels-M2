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
 * This class contains product delete
 */
class Productdelete implements ObserverInterface
{
    /**
     * Sending email for delete host product
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
       
        $product= $observer->getProduct(); 
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $property = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $product->getId() );
        $templateId = 'airhotels_product_admin_booking_delete_option';
        $propertyName = $property->getName ();
        $userId = $property->getUserId ();
        $customer = $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $userId );
        $recipient = $customer->getEmail ();
        $customerName = $customer->getName ();
        if (! empty ( $recipient ) && $userId ) {
            /**
             * Sending property delete email to host
             */
            $admin = $objectManager->get ( 'Apptha\Airhotels\Helper\Data' );
            /**
             * Assign admin details
             */
            $adminName = $admin->getAdminName ();
            $adminEmail = $admin->getAdminEmail ();
            /* Receiver Detail  */
            $receiverInfo = [
                'name' => $customerName,
                'email' =>$recipient
            ];
            /* Sender Detail  */
            $senderInfo = [
                'name' => $adminName,
                'email' => $adminEmail,
            ];
            $emailTempVariables = (array (
                'ownername' => $adminName,
                'pname' => $propertyName,
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