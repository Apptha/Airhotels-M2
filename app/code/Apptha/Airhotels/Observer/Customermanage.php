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
 * This class customer details
 */
class Customermanage implements ObserverInterface
{
    /**
     * update profile after customer save
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer= $observer->getCustomer(); 
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          /**
             * Load cusotmer profle collection
             * @var object $customerProfileModel
             */
            $customerProfileModel = $objectManager->get ( 'Apptha\Airhotels\Model\Customerprofile' )->load ( $customer->getId(), 'customer_id' );
            $customerProfileModel->setGender ($customer->getGender());
            $customerProfileModel->setDob ($customer->getDob());
            $customerProfileModel->save(); 
    }
}