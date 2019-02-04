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
namespace Apptha\Airhotels\Block\Listings;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\StockRegistry;
use Zend\Form\Annotation\Object;

/**
 * This class used to display the products collection
 */
class Search extends \Magento\Framework\View\Element\Template {
     /**
      * Initilize variable for product factory
      *
      * @var \Magento\Catalog\Model\ProductFactory
      */
     protected $productFactory;
     protected $_currency;
     /**
      * Initilize variable for stock registry
      *
      * @var Magento\CatalogInventory\Model\StockRegistry
      */
     protected $stockRegistry;
     protected $messageManager;
     /**
      *
      * @param Template\Context $context
      * @param ProductFactory $productFactory
      * @param array $data
      */
     public function __construct(Template\Context $context, Collection $productFactory, \Magento\Framework\App\Request\Http $request, \Magento\Customer\Model\Session $customerSession, \Magento\Directory\Model\Currency $currency, StockRegistry $stockRegistry, \Magento\Catalog\Model\Product $product, \Magento\Framework\Message\ManagerInterface $messageManager, array $data = []) {
          $this->productFactory = $productFactory;
          $this->product = $product;
          $this->customerSession = $customerSession;
          $this->messageManager = $messageManager;
          $this->stockRegistry = $stockRegistry;
          $this->_currency = $currency;
          $this->request = $request;
          $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          parent::__construct ( $context, $data );
     }
     /**
      * Set product collection uisng ProductFactory object
      *
      * @return void
      */
     protected function _construct() {
          parent::_construct ();
          $collection = $this->getFilterProducts ();
          $this->setCollection ( $collection );
     }
     /**
      * Getting website current date
      */
     public function currentDate() {
          $objDate = $this->_objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' );
          return $objDate->gmtDate ();
     }
     /**
      * Getting website media url
      */
     public function getMediaUrl() {
          $storeManager = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' );
          return $storeManager->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
     }
     /**
      * Getting website base url
      */
     public function getBaseUrl() {
          $storeManager = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' );
          return $storeManager->getStore ()->getBaseUrl ();
     }
     /**
      * Prepare layout for manage product
      *
      * @return object $this
      */
     protected function _prepareLayout() {
          parent::_prepareLayout ();
          /**
           *
           * @var \Magento\Theme\Block\Html\Pager
           */
          return $this;
     }
     /**
      * Get Manage product pager html
      *
      * @return string
      */
     public function getPagerHtml() {
          return $this->getChildHtml ( 'pager' );
     }
     /**
      * List all your host property
      *
      *
      * Return the property collection
      *
      * @return array
      */
     public function getListings() {
          /**
           * Getting customer session
           */
          $customer = $this->customerSession->getCustomer ();
          /**
           * Getting customer Id
           */
          $cusId = $customer->getId ();
          /**
           * return array
           */
          return $this->product->getCollection ()->addAttributeToFilter ( 'type_id', array (
                    'eq' => 'booking'
          ) )->addAttributeToSelect ( 'image' )->addAttributeToSelect ( 'user_id' )->addAttributeToSelect ( 'bedtype' )->addAttributeToSelect ( 'name' )->addAttributeToSelect ( 'price' )->addAttributeToSelect ( 'description' )->addAttributeToSelect ( 'short_description' )->addAttributeToSelect ( 'booking_type' )->addAttributeToSelect ( 'rooms' )->addAttributeToSelect ( 'propertyaddress' )->addAttributeToSelect ( 'privacy' )->addAttributeToSelect ( 'status' )->addAttributeToSelect ( 'city' )->addAttributeToSelect ( 'state' )->addAttributeToSelect ( 'country' )->addAttributeToSelect ( 'cancelpolicy' )->addAttributeToSelect ( 'pets' )->addAttributeToSelect ( 'maplocation' )->addAttributeToSelect ( 'accomodates' )->addAttributeToSelect ( 'property_approved' )->addFieldToFilter ( array (
                    array (
                              'attribute' => 'userid',
                              'eq' => $cusId
                    )
          ) );
     }
     /**
      * Getting the advance Search result
      */
     public function getAdvanceSearchResult() {
          /**
           * Get zoom level from parameters
           *
           * @var $zoomLevel
           */
          $zoomLevel = $this->getRequest ()->getParam ( 'zoomLevel' );
          $searchAddressFrom = $this->getRequest ()->getParam ( 'searchAddressFrom' );
          if (isset ( $zoomLevel )) {
               $address = $this->getRequest ()->getParam ( 'searchAddress' );
          } else {
               if (isset ( $searchAddressFrom )) {
                    $address = $searchAddressFrom;
               } else {
                    /**
                     * get Address
                     */
                    $address = $this->getRequest ()->getParam ( 'searchAddress' );
               }
          }
          /**
           * get Checkin
           */
          $checkin = $this->getRequest ()->getParam ( 'checkin' );
          /**
           * get Checkout
           */
          $checkout = $this->getRequest ()->getParam ( 'checkout' );
          /**
           * get Search Guest
           */
          $searchguest = $this->getRequest ()->getParam ( 'searchguest' );
          /**
           * get Amount
           */
          $amount = $this->getRequest ()->getParam ( 'amount' );
          /**
           * get Room type Value
           */
          $roomtypeVal = $this->getRequest ()->getParam ( 'roomtypeval' );
          /**
           * get Category Value
           */
          $categoryVal = $this->getRequest ()->getParam ( 'categoryVal' );
          /**
           * get amenity Value
           */
          $amenityval = $this->getRequest ()->getParam ( 'amenityval' );
          /**
           * get house_rules Value
           */
          $houseRulesval = $this->getRequest ()->getParam ( 'houseRulesval' );
          /**
           * get host_languages Value
           */
          $hostLanguagesval = $this->getRequest ()->getParam ( 'hostLanguagesval' );
          /**
           * get Property Details
           */
          $pageno = $this->getRequest ()->getParam ( 'pageno' );
          /**
           * Getting page number
           */
          $upperLimitPrice = $this->getRequest ()->getParam ( 'upperLimitPrice' );
          /**
           * Getting Property service from
           */
          $propertyServiceFrom = $this->getRequest ()->getParam ( 'propertyServiceFrom' );
          /**
           * Getting Property service to
           */
          $propertyServiceTo = $this->getRequest ()->getParam ( 'propertyServiceTo' );
          /**
           * Getting am/pm
           */
          $propertyServiceFromPeriod = $this->getRequest ()->getParam ( 'propertyServiceFromPeriod' );
          $propertyServiceToPeriod = $this->getRequest ()->getParam ( 'propertyServiceToPeriod' );
          /**
           * Get the latitute and longtitude
           */
          $lattitudeZoom = $this->getRequest ()->getParam ( 'latituteZoom' );
          /**
           * Getting lattitude Zoomlevel
           */
          $propertyType = $this->getRequest ()->getParam ( 'proptypeVal' );
          /**
           * make an data array
           */
          $data = array (
                    "address" => $address,
                    "checkin" => $checkin,
                    "checkout" => $checkout,
                    "searchguest" => $searchguest,
                    "amount" => $amount,
                    "pageno" => $pageno,
                    "roomtypeval" => $roomtypeVal,
                    "categoryVal" => $categoryVal,
                    "amenityVal" => $amenityval,
                    "hostLanguagesVal" => $hostLanguagesval,
                    "houseRulesVal" => $houseRulesval,
                    "upperLimitPrice" => $upperLimitPrice,
                    "propertyServiceFrom" => $propertyServiceFrom,
                    "propertyServiceTo" => $propertyServiceTo,
                    "propertyServiceFromPeriod" => $propertyServiceFromPeriod,
                    "propertyServiceToPeriod" => $propertyServiceToPeriod,
                    'latituteZoom' => $lattitudeZoom,
                    'zoomLevel' => $zoomLevel,
                    "booking_type" => $propertyType
          );
          /**
           * make sure the check in is present
           */
          if ($data ["checkin"] == "mm/dd/yyyy") {
               $data ["checkin"] = "";
          }
          /**
           * Make sure the checkout is present
           */
          if ($data ["checkout"] == "mm/dd/yyyy") {
               $data ["checkout"] = "";
          }
          /**
           * Check the value is present
           */
          if (trim ( $data ["address"] ) == "e.g. Berlin, Germany") {
               $data ["address"] = "";
          }
          /**
           * return array
           */
          return $this->_objectManager->get ( 'Apptha\Airhotels\Model\Search' )->advanceSearch ( $data );
     }
     /**
      * get the Readed emails
      *
      * @param int $inboxDetails
      * @param int $i
      * @return string
      */
     public function readClass($inboxDetails, $i) {
          /**
           * Inboxed message
           */
          if ($inboxDetails [$i] ["receiver_read"] == '1') {
               $readClass = "class='read'";
          } else {
               $readClass = "class='unread' ";
          }
          /**
           * Returns status of mail class
           */
          return $readClass;
     }
     /**
      * Get the sender readed emails
      *
      * @param int $inboxDetails
      * @param int $i
      * @return string
      */
     public function senderReadClass($inboxDetails, $i) {
          /**
           * Inboxed message read or unread
           */
          if ($inboxDetails [$i] ["sender_read"] == '1') {
               $readClass = "class='read'";
          } else {
               $readClass = "class='unread' ";
          }
          /**
           * Returns status of mail
           */
          return $readClass;
     }
     /**
      * Getting the image url status
      *
      * @param string $preImageUrl
      * @param string $currentImageUrl
      * @return number
      */
     public function imageUrlStatus($preImageUrl, $currentImageUrl) {
          /**
           * Getting the imageUrl
           */
          if ($preImageUrl == $currentImageUrl && $preImageUrl != '') {
               $imageUrlStatusForProperty = 1;
          } else {
               $imageUrlStatusForProperty = 0;
          }
          /**
           * Returns the image Url
           */
          return $imageUrlStatusForProperty;
     }
     /**
      * Function getGeocode()
      *
      * @param unknown $address
      * @return unknown
      */
     public function getGeocode($address) {
          /**
           * Check weather the 'allow_url_fopen' is enabled
           */
          $googleApiKey = $this->_objectManager->get ('Apptha\Airhotels\Helper\Order')->getGoogleapiKey();
          if (ini_get ( 'allow_url_fopen' )) {
               $geocode = file_get_contents ( 'https://maps.google.com/maps/api/geocode/json?address=' . urlencode ( $address ) . '&sensor=false&key='.$googleApiKey );
          } else {
               /**
                * Initialise the CURL
                */
               $ch = curl_init ();
               /**
                * passing the curl parameters
                */
               curl_setopt ( $ch, CURLOPT_URL, 'https://maps.google.com/maps/api/geocode/json?address=' . urlencode ( $address ) . '&sensor=false&key='.$googleApiKey );
               curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
               /**
                * Execute the CURL
                */
               $geocode = curl_exec ( $ch );
          }
          /**
           * decode the geocode
           *
           * @var $jsondata
           */
          $jsondata = json_decode ( $geocode, true );
          /**
           * city
           */
          foreach ( $jsondata ["results"] as $results ) {
               foreach ( $results ["address_components"] as $addresss ) {
                    if (in_array ( "locality", $addresss ["types"] )) {
                         $arrayAddress ['city'] = $addresss ["long_name"];
                    }
               }
          }
          /**
           * country
           */
          foreach ( $jsondata ["results"] as $resultKey ) {
               foreach ( $resultKey ["address_components"] as $addressKey ) {
                    if (in_array ( "administrative_area_level_1", $addressKey ["types"] )) {
                         $arrayAddress ['state'] = $addressKey ["long_name"];
                    }
               }
          }
          /**
           * country
           */
          foreach ( $jsondata ["results"] as $result ) {
               foreach ( $result ["address_components"] as $address1 ) {
                    if (in_array ( "country", $address1 ["types"] )) {
                         $arrayAddress ['country'] = $address1 ["long_name"];
                    }
               }
          }
          /**
           * Impload array array of address.
           */
          return implode ( ", ", $arrayAddress );
     }
     /**
      * Get current currency symbol
      */
     public function getCurrentCurrencySymbol() {
          $currencyCode = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore()->getCurrentCurrencyCode();
         return $this->_objectManager->get('Magento\Directory\Model\CurrencyFactory')->create()->load($currencyCode)->getCurrencySymbol();
     }
     /**
      * Get price slider value from admin
      */
     public function getPriceSlider() {
          return $this->_objectManager->get ( 'Apptha\Airhotels\Helper\Data' )->getPriceSlider ();
     }
     /**
      * Retrieve attribute id for languages
      *
      * Get geo codes
      *
      * @param unknown $propertyAddress
      * @return multitype:NULL unknown
      */
     public function getGeocodeDatas($propertyAddress) {
          $arrayAddress = $address = array ();
          $addrsRemoveSpace = str_replace ( ' ', ',', $propertyAddress );
          $addressAddPlus = str_replace ( ',', '+', $addrsRemoveSpace );
          $encodeAddress = urlencode ( $addressAddPlus );
          $googleApiKey = $this->_objectManager->get ('Apptha\Airhotels\Helper\Order')->getGoogleapiKey();
          /**
           * Google map API cal.
           */
          $geocode = file_get_contents ( 'https://maps.google.com/maps/api/geocode/json?address=' . rtrim ( $encodeAddress ) . '&sensor=false&key='.$googleApiKey);
          $jsondata = json_decode ( $geocode, true );
          /**
           * street
           */
          foreach ( $jsondata ["results"] as $resultsData ) {
               foreach ( $resultsData ['address_components'] as $address ) {
                    if (in_array ( "administrative_area_level_1", $address ["types"] )) {
                         $arrayAddress ['state'] = $address ["long_name"];
                    }
               }
          }
          /**
           * city
           */
          foreach ( $jsondata ["results"] as $resultKeyData ) {
               foreach ( $resultKeyData ['address_components'] as $addressKeyData ) {
                    if (in_array ( "locality", $addressKeyData ["types"] )) {
                         $arrayAddress ['city'] = $addressKeyData ["long_name"];
                    }
               }
          }
          /**
           * country
           */
          foreach ( $jsondata ["results"] as $resultData ) {
               foreach ( $resultData ['address_components'] as $addressCodeData ) {
                    if (in_array ( "country", $addressCodeData ["types"] )) {
                         $arrayAddress ['country'] = $addressCodeData ["long_name"];
                    }
               }
          }
          /**
           * country
           */
          foreach ( $jsondata ["results"] as $result ) {
               foreach ( $result ["geometry"] as $address ) {
                    if (isset ( $address ['northeast'] )) {
                         $arrayAddress ['northeastlat'] = $address ['northeast'] ['lat'];
                         $arrayAddress ['northeastlng'] = $address ['northeast'] ['lng'];
                         $arrayAddress ['southwestlat'] = $address ['southwest'] ['lat'];
                         $arrayAddress ['southwestlng'] = $address ['southwest'] ['lng'];
                    }
               }
          }
          /**
           * Return an address array.
           */
          return $arrayAddress;
     }
     public function toCurrentCurrency($price) {
          $toCurrency = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getCurrentCurrency ();
          return $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseCurrency ()->convert ( $price, $toCurrency );
     }
     public function customerWishlist($customer) {
          return $this->_objectManager->get ( '\Magento\Wishlist\Model\Wishlist' )->loadByCustomerId ( $customer->getId () );
     }
     /**
      * Funnction Name: getPropertyName
      * reduce Property name length
      *
      * @param string $propertyName
      *
      * @return multitype:string boolean
      */
     public function getPropertyName($propertyName) {
          $propertyNameSub = substr ( $propertyName, 0, 45 );
          if (strlen ( $propertyName ) > 45) {
               $propertyNameSub .= '...';
          }
        return $propertyNameSub;
    }
/**
      * Funnction Name: getResult
      * reduce Property name length
      *
      * @param string $product
      *
      */
    public function getResult($propertyId) {
         return $this->product->load ( $propertyId );
    }

    }
