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

/**
 * Custom Module Email helper
 */
class General extends \Magento\Framework\App\Helper\AbstractHelper
{
/**
     * Send host vaerification email
     *
     * @param documents $documentId
     * @param
     * action for verification $action
     */
    public function verifyHostMail($documentId, $action) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $documentCollection = $objectManager->get ( 'Apptha\Airhotels\Model\Verifyhost' )->load ( $documentId );

            /**
             * Get documentation id
             */
            if ($documentCollection ['id_type'] == 0) {
                $verification_id = 'Passport';
            } elseif ($documentCollection ['id_type'] == 1) {
                $verification_id = 'Identicard';
            } else {
                $verification_id = 'Driving Licence';
            }
            /**
             * Getting the Property templeId value
             */

            /* Receiver Detail  */
            $receiverInfo = [
                'name' =>$documentCollection ['host_name'],
                'email' =>$documentCollection ['host_email']
            ];

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();

             $admin = $objectManager->get ( 'Apptha\Airhotels\Helper\Data' );
            /**
             * Assign admin details
             */
            $adminName = $admin->getAdminName ();
            $adminEmail = $admin->getAdminEmail ();
            /* Sender Detail  */
            $senderInfo = [
                'name' => $adminName,
                'email' => $adminEmail,
            ];
            $templateId = 'airhotels_host_verify_email_template';
            $emailTempVariables = (array (
                'ownername' => $documentCollection ['host_name'],
                'action' => $action,
                'verification_id' => $verification_id
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
            return;

        }

        /**
         * Function Name: 'domainKey'
         * Generate domain key
         *
         * @return string
         */
        public function domainKey($tkey) {
             /**
              * set message.
              *
              * @var $message
              */
             $message = "EM-AIRHOTELS2MP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
             /**
              * Get the key Value
              */
             $tKeyCondition = strlen ( $tkey );
             for($i = 0; $i < $tKeyCondition; $i ++) {
                  $key_array [] = $tkey [$i];
             }
             $encriptMessage = "";
             $kPos = 0;
             /**
              * Set character string.
              */
             $charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
             /**
              * Character String for Value
              */
             $charsStrCondition = strlen ( $charsStr );
             for($i = 0; $i < $charsStrCondition; $i ++) {
                  $chars_array [] = $charsStr [$i];
             }
             /**
              * message Condition
              */
             $messageCondition = strlen ( $message );
             $countKeyArray = count ( $key_array );
             for($i = 0; $i < $messageCondition; $i ++) {
                  $char = substr ( $message, $i, 1 );
                  /**
                   * Get collection.
                  */
                  $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
                  $offset = $objectManager->get ( 'Apptha\Airhotels\Helper\Product' )->getOffset ( $key_array [$kPos], $char );
                  /**
                   * get Offset Value
                  */
                  $encriptMessage .= $chars_array [$offset];
                  $kPos ++;
                  if ($kPos >= $countKeyArray) {
                       $kPos = 0;
                  }
             }
             /**
              * Return encrypt message.
              */
             return $encriptMessage;
        }
        /**
         * Function Name: 'getPlaceholderImage'
         * Get place holder iamge
         *
         * @return string
         */
        public function getPlaceholderImage(){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $storeManager = $objectManager->get("\Magento\Store\Model\StoreManagerInterface");
            $mediaUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
            return $mediaUrl.'catalog/product/placeholder/'.\Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\Config\ScopeConfigInterface')
            ->getValue('catalog/placeholder/thumbnail_placeholder');
        }
        /**
         * Function to get product media url
         *
         * @return url
         */
        public function getMytripUrl() {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ()->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ();
            return $objectManager->getUrl('booking/mytrip/currenttrip/');
        }
         /**
      * Function Name: priceConverter
      *
      * @param string $price
      */
    public function priceConverter($price)
    {
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $priceHelper = $objectManager->get('Magento\Framework\Pricing\Helper\Data');
          return $priceHelper->currency($price, true, false);
    }
     /**
     * Get product data collection
     *
     * Passed the product id to get product details
     *
     * @param int $productId
     * Return product details as array
     * @return array
     */
    public function getProductData($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productData = $objectManager->get('Apptha\Airhotels\Block\Listings\Home');
        return $productData->getProduct($productId);
    }
     /**
     *
     * Return routername
     * @return string
     */
     public function getRouteName(){
         $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
         $request = $objectManager->get('\Magento\Framework\App\Request\Http');
        return $request->getRouteName();
     }

     /**
     * Function getPropertyDetails return the airhotels attributes
     *
     * @return object
     */
     public function getPropertyDetails(){
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          return $objectManager->get ( 'Apptha\Airhotels\Model\Attributes' );
     }
     /**
     * Function getAttributeDetails return the default custom attributes
     *
     * @return object
     */
     public function getAttributeDetails(){
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          return $objectManager->get('Magento\Eav\Model\Config' );
     }
      /**
     * Function getSearchCategory return the category object
     *
     * @return object
     */
     public function getSearchCategory(){
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          return $objectManager->get ( 'Apptha\Airhotels\Block\Booking\Form' );
     }

    }
