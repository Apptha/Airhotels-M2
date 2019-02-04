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
namespace Apptha\Airhotels\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
class Order extends \Magento\Framework\App\Helper\AbstractHelper {
    /**
    * host order management enable/disable
    */
    const XML_PATH_HOST_ORDER_MANAGE='airhotels/order/host_order';
    /**
    *  order sales email/name
    */
    const XML_SALES_EMAIL = 'trans_email/ident_sales/email';
    const XML_SALES_NAME = 'trans_email/ident_sales/name';
    /**
    * Get googlemap api key
    */
    const XML_PATH_GOOGLEMAP = 'airhotels/general/googlemap';

     protected $orderInfo;
     protected $_checkoutSession;
     protected $scopeConfig;
     /**
      * @param \Apptha\Airhotels\Model\Hostorder $orderInfo
      */
     public function __construct(\Apptha\Airhotels\Model\Hostorder $orderInfo, \Magento\Customer\Model\Session $customerSession,\Magento\Checkout\Model\Session $checkoutSession,ScopeConfigInterface $scopeConfig){
          $this->orderInfo = $orderInfo;
          $this->customerSession = $customerSession;
          $this->_checkoutSession = $checkoutSession;
          $this->scopeConfig = $scopeConfig;
     }

     /**
      * Getting order information for the host
      *
      * @param int $orderId OrderId
      * @param int $itemId Product Id
      *
      * @return array
      */
     public function getOrderInformation($orderId,$itemId=''){
          $orderInfo = $this->orderInfo->getCollection ()
                      ->addFieldToSelect ( '*' )
                      ->addFieldToFilter ( 'order_item_id', $orderId );
          if(!empty($itemId)){
               $orderInfo->addFieldToFilter ( 'entity_id', $itemId );
          }

          return $orderInfo;
     }

     /**
      * Get store currency symbol
      *
      * @return string
      */
     public function getCurrencySymbol(){
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $currencyCode = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getCurrentCurrencyCode();
          $currency = $objectManager->get('Magento\Directory\Model\CurrencyFactory')->create()->load($currencyCode);

          return $currency->getCurrencySymbol();
     }
     /**
     * Function to get cancel request status by ordrer id
     *@param int $orderId
     *
     * @return integer
     */
    public function getCancelRequestStatus($orderId){
        $hostOrderModel =  $this->orderInfo->load($orderId,'order_item_id');

        return $hostOrderModel->getCancelRequestStatus();

    }
    /**
     * Function to get is host view order
     *@param int $orderId
     *
     * @return integer
     */
    public function getIsHostManageOrder($orderId){
        $hostOrderModel =  $this->orderInfo->load($orderId,'order_item_id');
        $hostId=$hostOrderModel->getHostId();
        return ($hostId==$this->customerSession->getCustomerId());

    }
    /**
     * Function to get is service fee from checkout session
     *
     * @return object
     */
    public function getCheckoutServiceFee(){
        return $this->_checkoutSession->getServiceFee();

    }
    /**
      * Function to get the host to manage order or not
      */
     public function getHostOrderManage() {
          return $this->scopeConfig->getValue ( static::XML_PATH_HOST_ORDER_MANAGE, ScopeInterface::SCOPE_STORE );
     }
     /**
      * Function to get admin general name
      *
      * @return string
      */
     public function getStoreSalesName() {
         return $this->scopeConfig->getValue ( static::XML_SALES_NAME, ScopeInterface::SCOPE_STORE );
     }
     /**
      * Function to get the host to manage order or not
      */
     public function getStoreSalesEmail() {
         return $this->scopeConfig->getValue ( static::XML_SALES_EMAIL, ScopeInterface::SCOPE_STORE );
     }
     /**
      * Function to get the store name
      */
     public function getStoreName() {
         $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
         $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
         return $storeManager->getStore()->getName();
     }
     /**
     * Function to get service fee
     *
     * @return boolean
     */
    public function getGoogleapiKey() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue ( static::XML_PATH_GOOGLEMAP, $storeScope );
    }
    /**
      * Get order currency symbol
      *
      * @return string
      */
     public function getOrderCurrencySymbol($code){
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $currency = $objectManager->get('Magento\Directory\Model\CurrencyFactory')->create()->load($code);

          return $currency->getCurrencySymbol();
     }
}
