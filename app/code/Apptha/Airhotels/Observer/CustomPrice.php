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
 * This class contains special price details functions
 */
class CustomPrice implements ObserverInterface
    {
     /**
     * updating the special price to product after booking the listing
     */   
        public function execute(\Magento\Framework\Event\Observer $observer) {
            $item = $observer->getEvent()->getData('quote_item');         
            $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
            //instance of object manager
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $baseCurrencyCode = $objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore()->getBaseCurrencyCode();
            $currentCurrencyCode = $objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore()->getCurrentCurrencyCode();
            //checkout session
            $checkoutSession = $objectManager->get('Magento\Checkout\Model\Session');
            $price = $checkoutSession->getAnyBaseSubtotal();           
            if ($baseCurrencyCode !== $currentCurrencyCode) {                 
               
            $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
            $formattedPrice = $priceHelper->currency($price, false, false);            
            } else { 
            $formattedPrice = $price;
            }
            $item->setCustomPrice($formattedPrice);
            $item->setOriginalCustomPrice($formattedPrice);
            $item->setQty(0);
            $item->getProduct()->setIsSuperMode(true);
        } 
    }
?>
