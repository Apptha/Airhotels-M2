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

class Product extends \Magento\Framework\App\Helper\AbstractHelper {
             
     
     protected $reviewFactory;
     protected $storeManager;
     protected $request;
      
     public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager,\Magento\Review\Model\ReviewFactory $reviewFactory,\Magento\Framework\App\Request\Http $request){
          $this->storeManager = $storeManager;
          $this->reviewFactory = $reviewFactory;
          $this->request = $request;
     }
     public function getReviewDetails($productId){
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $rating = $objectManager->get("Magento\Review\Model\ResourceModel\Review\CollectionFactory");
          return $rating->create()
          ->addStatusFilter(
                    \Magento\Review\Model\Review::STATUS_APPROVED
                    )->addEntityFilter(
                              'product',
                              $productId
                              )->setDateOrder()->addRateVotes ();
     }
     /**
      * Function Name: 'AirhotelsKey'
      * Retrieve Airhotels key
      *
      * @return string
      */
     public function AirhotelsKey() {
          /**
           * Call the 'genenrateAirhotelsdomain' Method
           */
          $code = $this->genenrateAirhotelsdomain ();
          /**
           * create the Domain key for the Website
          */
          $domainKey = substr ( $code, 0, 25 ) . "CONTUS";
          /**
           * get the API key
           */
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          $emailHelper = $objectManager->get ( 'Apptha\Airhotels\Helper\Email' );
          $storeId = $emailHelper->getStore()->getId();
          $apikey = $emailHelper->getConfigValue('airhotels/general/license_key', $storeId);
          /**
           * Check weather the 'domainkey' and 'apikey' is not same
          */
          if ($domainKey != $apikey) {
               return base64_decode ( 'PGgzIHN0eWxlPSJmbG9hdDpsZWZ0O2NvbG9yOnJlZDttYXJnaW46IDMwMHB4IDQ5MHB4OyB3aWR0aDoyNiU7IHRleHQtZGVjb3JhdGlvbjp1bmRlcmxpbmU7IiBpZD0idGl0bGUtdGV4dCI+PGEgdGFyZ2V0PSJfYmxhbmsiIGhyZWY9Imh0dHA6Ly93d3cuYXBwdGhhLmNvbS9jaGVja291dC9jYXJ0L2FkZC9wcm9kdWN0LzE5MC9xdHkvMSIgc3R5bGU9ImNvbG9yOnJlZDsiPkludmFsaWQgTGljZW5zZSBLZXkgLSBCdXkgbm93PC9hPjwvaDM+' );
          }
     }
     
     /**
      * Function Name: 'genenrateAirhotelsdomain'
      * Generate domain based key
      *
      * @var $subfolderValue
      * @var $strDomainNameValue
      * @var $customerurlValue
      * @var $responseVal
      *
      *
      * @return string
      */
     public function genenrateAirhotelsdomain() {
          /**
           * Initialise the '$subfolderValue', '$matchesArray'
           */
          $subfolderValue = $matchesArray = '';
          /**
           * Get the String DOmain Name Value
           */
          $strDomainNameValue = $this->request->getHttpHost ();
          preg_match ( "/^(http:\/\/)?([^\/]+)/i", $strDomainNameValue, $subfolderValue );
          preg_match ( "/^(https:\/\/)?([^\/]+)/i", $strDomainNameValue, $subfolderValue );
          preg_match ( "/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolderValue [2], $matchesArray );
          if (isset ( $matchesArray ['domain'] )) {
               $customerurlValue = $matchesArray ['domain'];
          } else {
               $customerurlValue = "";
          }
          /**
           * get the Customer Value
           */
          $customerurlValue = str_replace ( "www.", "", $customerurlValue );
          $customerurlValue = str_replace ( ".", "D", $customerurlValue );
          $customerurlValue = strtoupper ( $customerurlValue );
          if (isset ( $matchesArray ['domain'] )) {
               $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
               $responseVal = $objectManager->get ( 'Apptha\Airhotels\Helper\General' )->domainKey ( $customerurlValue );
          } else {
               $responseVal = "";
          }
          /**
           * Return response value.
           */
          return $responseVal;
     }
     /**
      * Function Name: 'getOffset'
      * Retrieve key
      *
      * @return string
      */
     public function getOffset($start, $end) {
          /**
           * Define character string.
           */
          $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
          $charsStr = strlen ( $chars_str );
          for($i = 0; $i < $charsStr; $i ++) {
               $chars_array [] = $chars_str [$i];
          }
     
          for($i = count ( $chars_array ) - 1; $i >= 0; $i --) {
               $lookupObj [ord ( $chars_array [$i] )] = $i;
          }
          /**
           * Get the $sNum, $eNum Values
           */
          $sNum = $lookupObj [ord ( $start )];
          $eNum = $lookupObj [ord ( $end )];
     
          $offset = $eNum - $sNum;
     
          if ($offset < 0) {
               $offset = count ( $chars_array ) + ($offset);
          }
          /**
           * Returning the Offset Values
           */
          return $offset;
     }
     
     /**
      * Get rating summary for the product.
      *
      * @param array $product Product object
      *
      * @return int
      */
     public function getRatingSummary($product)
     {
         $storeId = $this->storeManager->getStore ()->getId ();
         $this->reviewFactory->create ()->getEntitySummary ( $product, $storeId );
         
         return $product->getRatingSummary ()->getRatingSummary ();
     }
    
}