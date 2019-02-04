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
namespace Apptha\Airhotels\Model;

/**
 * Airhotels search model
 */
class Search extends \Magento\Framework\DataObject {
     
     /**
      *
      * @var \Magento\Store\Model\StoreManagerInterface
      */
     protected $_storeManager;
     /**
      *
      * @var \Magento\Framework\App\Config\ValueInterface
      */
     protected $_backendModel;
     /**
      *
      * @var \Magento\Framework\App\Config\ScopeConfigInterface
      */
     protected $_scopeConfig;
     /**
      *
      * @var \Magento\Framework\App\Config\ValueFactory
      */
     protected $_configValueFactory;
     /**
      *
      * @var string $_storeCode
      */
     protected $_storeCode;
     /**
      *
      * @var int $_storeId
      */
     protected $_storeId;

     protected $_date;
     
     /**
      *
      * @param \Magento\Store\Model\StoreManagerInterface $storeManager,             
      * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,             
      * @param \Magento\Framework\App\Config\ValueInterface $backendModel,             
      * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory,             
      * @param array $data             
      */
     public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\ResourceConnection $resource, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\App\Config\ValueInterface $backendModel, \Magento\Checkout\Model\Session $checkoutSession, \Apptha\Airhotels\Helper\Dateformat $_date, \Magento\Framework\Controller\Result\JsonFactory $jsonResult, \Magento\Catalog\Model\Product $product, \Magento\Framework\App\Config\ValueFactory $configValueFactory, array $data = []) {
          parent::__construct ( $data );
          $this->_storeManager = $storeManager;
          $this->_scopeConfig = $scopeConfig;
          $this->_backendModel = $backendModel;
          $this->checkoutSession = $checkoutSession;
          $this->_resource = $resource;
          $this->jsonResult = $jsonResult;
          $this->_configValueFactory = $configValueFactory;
          $this->_storeId = ( int ) $this->_storeManager->getStore ()->getId ();
          $this->_storeCode = $this->_storeManager->getStore ()->getCode ();
          $this->product = $product;
          $this->_date = $_date;
          $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
     }
     
     /**
      * function to search property
      */
     public function advanceSearch($data) {
          /**
           * Initilizing price for filter
           */
          $amount = explode ( "-", $data ["amount"] );
          $minval = $amount [0];
          $maxval = $amount [1];
          /**
           * Filter by booking enable
           */
          $copycollection = $this->objectManager->get ( '\Magento\Catalog\Model\Product' )->getCollection ()->addAttributeToSelect ( '*' )->addFieldToFilter ( array (
                    array (
                              'attribute' => 'status',
                              'eq' => '1' 
                    ) 
          ) )->addFieldToFilter ( array (
                    array (
                              'attribute' => 'property_approved',
                              'eq' => '1' 
                    ) 
          ) );
          /**
           * Filter by price
           */
          $copycollection->setOrder ( 'price', 'desc' );
          $copycollection->addFieldToFilter ( 'price', array (
                    'gteq' => $minval 
          ) );
          $copycollection->addFieldToFilter ( 'price', array (
                    'lteq' => $maxval 
          ) );
          /**
           * Filter by city, state, country value from address value
           */
          $copycollection = $this->bookingTypeFilter ( $data, $copycollection );
          $copycollection = $this->categoryFilter ( $data, $copycollection );
          $copycollection = $this->searchResult ( $data, $copycollection );
          
          if ($data ["amenityVal"] != '') {
               /**
                * Filter by amenity
                */
               $amenityString = $data ["amenityVal"];
               $amenityArray = explode ( ",", $amenityString );
               if (count ( $amenityArray ) >= 1) {
                    foreach ( $amenityArray as $amenity ) {
                         $copycollection->addFieldToFilter ( array (
                                   array (
                                             'attribute' => 'amenity',
                                             'like' => "%$amenity%" 
                                   ) 
                         ) );
                    }
               } else {
                    $copycollection->addFieldToFilter ( array (
                              array (
                                        'attribute' => 'amenity',
                                        'like' => "%$amenityString%" 
                              ) 
                    ) );
               }
          }
          
          if ($data ["houseRulesVal"] != '') {
               /**
                * Filter by house_rules
                */
               $houseRulesString = $data ["houseRulesVal"];
               $houseRulesArray = explode ( ",", $houseRulesString );
               if (count ( $houseRulesArray ) >= 1) {
                    foreach ( $houseRulesArray as $houseRules ) {
                         $copycollection->addFieldToFilter ( array (
                                   array (
                                             'attribute' => 'house_rules',
                                             'like' => "%$houseRules%" 
                                   ) 
                         ) );
                    }
               } else {
                    $copycollection->addFieldToFilter ( array (
                              array (
                                        'attribute' => 'house_rules',
                                        'like' => "%$houseRulesString%" 
                              ) 
                    ) );
               }
          }
          
          if ($data ["hostLanguagesVal"] != '') {
               /**
                * Filter by host_langugages
                */
               $hostLanguagesString = $data ["hostLanguagesVal"];
               $hostLanguagesArray = explode ( ",", $hostLanguagesString );
               if (count ( $hostLanguagesArray ) >= 1) {
                    foreach ( $hostLanguagesArray as $hostLanguages ) {
                         $copycollection->addFieldToFilter ( array (
                                   array (
                                             'attribute' => 'host_languages',
                                             'like' => "%$hostLanguages%" 
                                   ) 
                         ) );
                    }
               } else {
                    $copycollection->addFieldToFilter ( array (
                              array (
                                        'attribute' => 'host_languages',
                                        'like' => "%$hostLanguagesString%" 
                              ) 
                    ) );
               }
          }
          
          $copycollection = $this->availableProducts ( $data, $copycollection );
          /**
           * Set page size for display result
           */
          $copycollection->setPage ( $data ["pageno"], 6 );
          return $copycollection;
     }
     /**
      * Function Name: categoryFilter
      *
      * @param
      *             $data,$collection,$copycollection
      */
     public function categoryFilter($data, $copycollection) {
          
          /**
           * Filter by category
           */
          $categoryValString = $data ["categoryVal"];
          $categoryVal = explode ( ",", $categoryValString );
          if (count ( $categoryVal ) > 0 && trim ( $categoryValString ) != "") {
               $copycollection->addCategoriesFilter ( array (
                         'in' => $categoryVal 
               ) );
          }
          
          return $copycollection;
     }
     /**
      * Function Name: bookingTypeFilter
      *
      * @param
      *             $data,$collection,$copycollection
      */
     public function bookingTypeFilter($data, $copycollection) {
          /**
           * Filter by seats
           */
          if (( int ) $data ["searchguest"] > 0) {
               if (( int ) $data ["searchguest"] >= 16) {
                    $copycollection->addFieldToFilter ( 'accommodate_minimum', array (
                              'gteq' => ( int ) $data ["searchguest"] 
                    ) );
               } else {
                    $copycollection->addFieldToFilter ( 'accommodate_minimum', array (
                              'lteq' => ( int ) $data ["searchguest"] 
                    ) );
                    $copycollection->addFieldToFilter ( 'accommodate_maximum', array (
                              'gteq' => ( int ) $data ["searchguest"] 
                    ) );
               }
          }
          /**
           * Filter by property type
           */
          $roomtypeString = $data ["booking_type"];
          $dataRoomtypeval = explode ( ",", $roomtypeString );
          if (count ( $dataRoomtypeval ) > 0 && trim ( $roomtypeString ) != "") {
               $copycollection->addFieldToFilter ( 'booking_type', array (
                         'in' => array (
                                   $dataRoomtypeval 
                         ) 
               ) );
          }
          /**
           * Filter by privacy
           */
          $roomtypeval = $data ["roomtypeval"];
          $dataRoomtype = explode ( ",", $roomtypeval );
          if (count ( $dataRoomtype ) > 0 && trim ( $roomtypeval ) != "") {
               $copycollection->addFieldToFilter ( 'privacy', array (
                         'in' => array (
                                   $dataRoomtype 
                         ) 
               ) );
          }
          return $copycollection;
     }
     
     /**
      * Function searchResult
      *
      * @param
      *             $data,$copycollection
      */
     public function searchResult($data, $copycollection) {
          
          /**
           * Check weather the latitude zoom and address are set.
           */
          $googleApiKey = $this->objectManager->get ('Apptha\Airhotels\Helper\Order')->getGoogleapiKey();
          $zoomLevel = $data ['zoomLevel'];
          if (! empty ( $data ["latituteZoom"] ) && $data ["address"] == '') {
               $latitueAndLong = explode ( ",", $data ["latituteZoom"] );
               $zoomlevelArray = array (
                         0 => '10000',
                         1 => '5000',
                         2 => '3000',
                         3 => '1500',
                         4 => '1000',
                         5 => '500',
                         6 => '400',
                         7 => '300',
                         8 => '200',
                         9 => '100',
                         10 => '40',
                         11 => '15',
                         12 => '7',
                         13 => '5',
                         14 => '2',
                         15 => '1',
                         16 => '0.75',
                         17 => '0.50',
                         18 => '0.25' 
               );
               
               $lat = $latitueAndLong [0];
               $long = $latitueAndLong [1];
               $collectionLat = $this->objectManager->get ( 'Apptha\Airhotels\Model\Longitude' )->getCollection ();
               $collectionLat->addExpressionFieldToSelect ( 'distance', '( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( {{latitude}}) ) * cos( radians( {{longitude}}) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( {{latitude}}) ) ) )', array (
                         'latitude' => 'latitude',
                         'longitude' => 'longitude' 
               ) );
               $collectionLat->getSelect ()->having ( 'distance < ' . $zoomlevelArray [$zoomLevel] );
               $collectionLatDataValue = $collectionLat->getData ();
               $nearesEntityVal = '';
               foreach ( $collectionLatDataValue as $entitiesArray ) {
                    $nearesEntityVal .= $entitiesArray ['entity_id'] . ',';
               }
               $nearesEntityVal = rtrim ( $nearesEntityVal, ',' );
               $entityIdsArr = '';
               $entityidsVal = explode ( ',', $nearesEntityVal );
               foreach ( $entityidsVal as $ids ) {
                    $entityIdsArr [] = array (
                              'attribute' => 'entity_id',
                              'in' => $ids 
                    );
               }
               $copycollection->addFieldToFilter ( $entityIdsArr );
          } else {
               
               if ($data ["address"] != '' || $addressTrimData != __ ( 'Berlin, San Juan de Lurigancho, Peru' )) {
                    $addressTrimData = trim ( $data ["address"] );
                    $address = $addressTrimData;
               } else {
                    $address = $this->objectManager->get ( 'Apptha\Airhotels\Helper\Data' )->getDefaultLocation ();
               }
               $country = $address;
               $addrsRemoveSpace = str_replace ( ' ', '+', $country );
               $addressAddPlus = str_replace ( ',', '+', $addrsRemoveSpace );
               /**
                * Check weahter 'allow_url_fopen' is enabled
                */
               $encodeAddress = urlencode ( $addressAddPlus );
               if (ini_get ( 'allow_url_fopen' )) {
                    $geocode = file_get_contents ( 'https://maps.google.com/maps/api/geocode/json?address=' . rtrim ( $encodeAddress ) . '&sensor=false&key='.$googleApiKey );
               } else {
                    $ch = curl_init ();
                    curl_setopt ( $ch, CURLOPT_URL, 'https://maps.google.com/maps/api/geocode/json?address=' . rtrim ( $encodeAddress ) . '&sensor=false&key='.$googleApiKey );
                    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
                    $geocode = curl_exec ( $ch );
               }
               /**
                * get the geo code values.
                */
               $output = json_decode ( $geocode, true );
               if ($output ['status'] == 'OK') {
                    $northeastlat = $output ['results'] ['0'] ['geometry'] ['viewport'] ['northeast'] ['lat'];
                    $northeastlng = $output ['results'] ['0'] ['geometry'] ['viewport'] ['northeast'] ['lng'];
                    $southwestlat = $output ['results'] ['0'] ['geometry'] ['viewport'] ['southwest'] ['lat'];
                    $southwestlng = $output ['results'] ['0'] ['geometry'] ['viewport'] ['southwest'] ['lng'];
                    $maxLatitudeValue = max ( $northeastlat, $southwestlat );
                    $minLatitudeValue = min ( $northeastlat, $southwestlat );
                    $maxLongitudeValue = max ( $northeastlng, $southwestlng );
                    $minLongitudeValue = min ( $northeastlng, $southwestlng );
               } else {
                    $northeastlat = $northeastlng = $southwestlat = $southwestlng = NULL;
                    $maxLatitudeValue = $minLatitudeValue = $maxLongitudeValue = $minLongitudeValue = 0;
               }
               $copycollection->addAttributeToFilter ( array (
                         array (
                                   'attribute' => 'latitude',
                                   'lteq' => $maxLatitudeValue 
                         ) 
               ) );
               
               $copycollection->addAttributeToFilter ( array (
                         array (
                                   'attribute' => 'longitude',
                                   'lteq' => $maxLongitudeValue 
                         ) 
               ) );
               
               $copycollection->addAttributeToFilter ( array (
                         array (
                                   'attribute' => 'latitude', 
                                   'gteq' => $minLatitudeValue 
                         ) 
               ) );
               
               $copycollection->addAttributeToFilter ( array (
                         array (
                                   'attribute' => 'longitude',
                                   'gteq' => $minLongitudeValue 
                         ) 
               ) );
          }
          return $copycollection;
     }
     
     /**
      * Function Name: availableProducts
      *
      * @param
      *             $data,$copycollection,$collection
      */
     public function availableProducts($data, $copycollection) {
          /**
           * Filter by date and hour
           */
          /**
           * Initilizing date for filter
           */
          if ($data ['checkin'] != "") {
               $fromdate = $this->_date->searchDateFormat($data ['checkin']);
          }
          if ($data ["checkout"] != "") {
               $todate = $this->_date->searchDateFormat($data ['checkout']);
          }          
          /**
           * Declare $productFilter array
           *
           * @var unknown
           */
          $productFilter = array ();
          $count = 0;
          $bookingServiceFrom = $data ['propertyServiceFrom'];
          $bookingServiceTo = $data ['propertyServiceTo'];
          $bookingServiceFromPeriod = $data ['propertyServiceFromPeriod'];
          $bookingServiceToPeriod = $data ['propertyServiceToPeriod'];
          if (isset ( $fromdate ) && isset ( $todate ) && count ( $copycollection )) {
               foreach ( $copycollection as $_product ) {
                    /**
                     * checking whether hourly or daily based availability
                     */
                    if (( int ) $bookingServiceFrom > 0 && ( int ) $bookingServiceTo > 0 && ! empty ( $bookingServiceFromPeriod ) && ! empty ( $bookingServiceToPeriod )) {
                         /**
                          * Checking whether hourly or daily based product
                          */
                         
                         $availresult = $availresultCal = 1;
                    } else {
                         
                         $availresult = ( int ) $this->checkAvailableProduct ( $_product->getId (), $fromdate, $todate );
                         $availresultCal = ( int ) $this->checkavalidateincal ( $_product->getId (), $fromdate, $todate );
                    }
                    if (! $availresult || ! $availresultCal) {
                         $productFilter [$count] = $_product->getId ();
                         $count ++;
                    }
               }
          }
          /**
           * Filter by product id
           */
          if (count ( $productFilter )) {
               $copycollection = $copycollection->addFieldToFilter ( 'entity_id', array (
                         'nin' => $productFilter 
               ) );
               foreach ( $productFilter as $key ) {
                    $copycollection->removeItemByKey ( $key );
               }
          }
          return $copycollection;
     }
     
     /**
      * Function Name: checkAvailableProduct
      *
      * @param int $productid             
      * @param date $fromdate             
      * @param date $todate             
      * @return boolean
      */
     public function checkAvailableProduct($productid, $fromdate = "", $todate = "") {
          /**
           * Get the Customer Current Values
           */
          $myCalendar = $this->objectManager->get ( 'Apptha\Airhotels\Model\Checkavail' )->dateVerfiy ( $productid, $fromdate, $todate );
          /**
           * Get the Blocked Date
           */
          $blocked = $this->objectManager->get ( 'Apptha\Airhotels\Model\Booking' )->getDays ( count ( $myCalendar [1] ), $myCalendar [1] );
          /**
           * Get the not available value
           */
          $notAvail = $this->objectManager->get ( 'Apptha\Airhotels\Model\Booking' )->getDays ( count ( $myCalendar [2] ), $myCalendar [2] );
          /**
           * Checkin selected date
           */
          $From = date ( 'Y-n', strtotime ( $fromdate ) );
          /**
           * Checkout selected date
           */
          $To = date ( 'Y-n', strtotime ( $todate ) );
          $dateFrom = explode ( "-", $From );
          $dateTo = explode ( "-", $To );
          /**
           * Setting array with dealstatus with following options
           * 'processing' , 'complete'
           */
          $dealstatus = array (
                    'processing',
                    'complete' 
          );
          /**
           * Get the collection for 'airhotels/airhotels'
           */
          $ranges = $this->objectManager->get ( 'Apptha\Airhotels\Model\Hostorder' )->getCollection ()->addFieldToSelect ( array (
                    'entity_id',
                    'fromdate',
                    'todate',
                    'order_id',
                    'order_item_id' 
          ) )->addFieldToFilter ( 'order_status', array (
                    'eq' => 'complete' 
          ) );
          /**
           * Setting the Sales_Flat_order Value
           */
          $salesFlatOrder = ( string ) $this->_resource->getConnection ()->getTableName ( 'sales_order' );
          $ranges->getSelect ()->join ( array (
                    'sales_order' => $salesFlatOrder 
          ), "(sales_order.entity_id = main_table.order_item_id AND main_table.entity_id = $productid  AND (sales_order.status='$dealstatus[1]' OR sales_order.status='$dealstatus[0]'))", array () );
          $collection = array ();
          $c = 1;
          foreach ( $ranges as $range ) {
               if ($range ['fromdate'] != "") {
                    $collection [$c] = $range ['fromdate'];
                    $c ++;
               }
               if ($range ['todate']) {
                    $collection [$c] = $range ['todate'];
                    $c ++;
               }
          }
          /**
           * Call the 'airhotels/calendarsync' for colletion info.
           */
          return $this->getCollectionInfo ( $collection, $fromdate, $todate, $dateFrom, $dateTo, $blocked, $notAvail );
     }
     
     /**
      * Function Name: checkavalidateincal
      * Check avalilable date in calendar
      *
      * @param int $productid             
      * @param date $fromdate             
      * @param date $todate             
      * @return boolean
      */
     public function checkavalidateincal($productid, $fromdate = "", $todate = "") {
          $myCalendar = $this->dateVerfiyAdvanced ( $productid, $fromdate, $todate );
          $day = 86400;
          /**
           * Start as time
           */
          $sTime = strtotime ( $fromdate );
          /**
           * End as time
           */
          $eTime = strtotime ( $todate );
          $numDay = round ( ($eTime - $sTime) / $day ) + 1;
          /**
           * Get days
           */
          $currentMonthVal = '';
          $currentYearVal = '';
          /**
           * Iterating For loop
           */
          for($d1 = 0; $d1 < $numDay; $d1 ++) {
               date ( 'm/d/Y', ($sTime + ($d1 * $day)) );
               $checkingMonth = date ( 'm', ($sTime + ($d1 * $day)) );
               $checkingYear = date ( 'Y', ($sTime + ($d1 * $day)) );
               if (empty ( $currentMonthVal ) && empty ( $currentYearVal ) || $currentMonthVal != $checkingMonth || $currentYearVal != $checkingYear) {
                    $blocked = $notAvail = array ();
                    /**
                     * Get blocked collection.
                     */
                    $blocked = $this->getDaysAdvancedSearch ( count ( $myCalendar [1] ), $myCalendar [1], $checkingMonth, $checkingYear );
                    $notAvail = $this->getDaysAdvancedSearch ( count ( $myCalendar [2] ), $myCalendar [2], $checkingMonth, $checkingYear );
                    $currentMonthVal = $checkingMonth;
                    $currentYearVal = $checkingYear;
               }
               $dIn = date ( 'd', ($sTime + ($d1 * $day)) );
               if (in_array ( $dIn, $blocked ) || in_array ( $dIn, $notAvail )) {
                    return false;
               }
          }
          return true;
     }
     
     /**
      * Function Name: getCollectionInfo
      *
      * get Collection Information
      *
      * @param array $collection             
      * @param date $fromdate             
      * @param date $todate             
      * @param date $dateFrom             
      * @param date $dateTo             
      * @param array $blocked             
      * @param array $not_avail             
      * @return boolean
      */
     public function getCollectionInfo($collection, $fromdate, $todate, $dateFrom, $dateTo, $blocked, $not_avail) {
          /**
           * Initialise the vlaue to '$availabilityFrom' and '$availabilityTo'
           */
          $availabilityFrom = false;
          $availabilityTo = false;
          /**
           * Count the colletion Vlaue.
           */
          if (count ( $collection )) {
               $collectionCount = count ( $collection );
               for($i = 1; $i <= $collectionCount; $i += 2) {
                    $myDates = date ( 'Y-n-d', strtotime ( $collection [$i] ) );
                    $myMonth = explode ( "-", $myDates );
                    $availabilityFrom = $this->availabilityFrom ( $collection, $i, $fromdate );
                    $availabilityTo = $this->availabilityTo ( $collection, $i, $todate );
                    if (($myMonth [1] == $dateFrom [1]) && ($myMonth [0] == $dateTo [0]) && array_search ( $myMonth [2], $blocked ) || array_search ( $myMonth [2], $not_avail )) {
                         $availabilityFrom = false;
                         $availabilityTo = false;
                    }
                    /**
                     * ge tthe colletion of 'airhotels/customerreply'
                     * 'availability_from'
                     * 'availability_to'
                     */
                    if ((! $availabilityFrom) || (! $availabilityTo)) {
                         return false;
                    }
               }
          } else {
               $availabilityFrom = true;
               $availabilityTo = true;
          }
          /**
           * Return availability.
           */
          return $availabilityFrom;
     }
     
     /**
      *
      * @param Arrray $collection             
      * @param int $i             
      * @param date $fromdate             
      * @return boolean
      */
     public function availabilityFrom($collection, $i, $fromdate) {
          /**
           * Check date range.
           */
          return $this->check_in_range ( $collection [$i], $collection [$i + 1], $fromdate );
     }
     /**
      *
      * @param Arrray $collection             
      * @param int $i             
      * @param date $todate             
      * @return boolean
      */
     public function availabilityTo($collection, $i, $todate) {
          $availabilityTo = false;
          if ($this->check_in_range ( $collection [$i], $collection [$i + 1], $todate )) {
               $availabilityTo = true;
          }
          /**
           * Return availablility.
           */
          return $availabilityTo;
     }
     
     /**
      * Function Name: check_in_range
      * Check In Range
      *
      * @param date $start_date             
      * @param date $end_date             
      * @param date $date_from_user             
      * @return boolean
      */
     public function check_in_range($startDate, $endDate, $dateFromUser) {
          /**
           * Convert to timestamp
           */
          $startTime = strtotime ( $startDate );
          $endTime = strtotime ( $endDate );
          $userTime = strtotime ( $dateFromUser );
          /**
           * Check that user date is between start & end
           */
          $returnValue = true;
          if ((($userTime > $startTime) && ($userTime < $endTime)) || (($userTime == $startTime) || ($userTime == $endTime))) {
               $returnValue = false;
          }
          return $returnValue;
     }
     
     /**
      * Function Name: dateVerfiyAdvanced
      * Dateverify Advanced has used to verifying the advanced datas
      *
      * @param int $productid             
      * @param date $from             
      * @param date $to             
      *
      * @return array $calendar
      */
     public function dateVerfiyAdvanced($productId, $from, $to) {
          $day = 86400;
          $sTime = strtotime ( $from );
          $eTime = strtotime ( $to );
          $numDay = round ( ($eTime - $sTime) / $day ) + 1;
          $month = $year = array ();
          $currentMonth = $currentYear = '';
          for($d1 = 0; $d1 < $numDay; $d1 ++) {
               date ( 'Y-n', ($sTime + ($d1 * $day)) );
               $checkingMonth = date ( 'n', ($sTime + ($d1 * $day)) );
               $checkingYear = date ( 'Y', ($eTime + ($d1 * $day)) );
               if (empty ( $currentMonth ) || empty ( $currentYear ) || $currentMonth != $checkingMonth || $currentYear != $checkingYear) {
                    $month [] = $checkingMonth;
                    $year [] = $checkingYear;
                    $currentMonth = $checkingMonth;
                    $currentYear = $checkingYear;
               }
          }
          /**
           * Return blocked dates.
           */
          return $this->getBlockdateAdvanced ( $productId, $month, $year );
     }
     
     /**
      * Function Name: getDaysAdvancedSearch
      * Get the advance search result.
      *
      * @param int $count             
      * @param int $value             
      * @param int $checkingMonth             
      * @param int $checkingYear             
      * @return multitype:
      */
     public function getDaysAdvancedSearch($count, $value, $checkingMonth, $checkingYear) {
          $availDay = array ();
          for($j = 0; $j < $count; $j ++) {
               if ($value [$j] [2] == $checkingMonth && $value [$j] [3] == $checkingYear) {
                    $availDay [] = $value [$j] [1];
               }
          }
          /**
           * Return an array.
           */
          return explode ( ",", implode ( ",", $availDay ) );
     }
     
     /**
      * Get day wise booked array by product id
      *
      * @param int $productid             
      * @param date $date             
      * @param date $to             
      * @return array $datesRange
      */
     public function getBlockdateAdvanced($productid, $date, $to) {
          $datesBooked = $datesAvailable = $datesNotAvailable = array ();
          
          $months = $date;
          $year = $to;
          $inc = 0;
          foreach ( $months as $month ) {
               /**
                * Get daywise blocked details
                */
               $yearVal = $year [$inc];
               $result = $this->objectManager->get ( 'Apptha\Airhotels\Model\Calendar' )->getCollection ()->addFieldToFilter ( 'month', $month )->addFieldToFilter ( 'year', $yearVal )->addFieldToFilter ( 'product_id', $productid )->addFieldToFilter ( 'blockfrom', array (
                         'neq' => '' 
               ) );
               foreach ( $result as $res ) {
                    $bookavail = $res ['book_avail'];
                    $fromdate = $res ['blockfrom'];
                    $month = $res ['month'];
                    $price = $res ['price'];
                    if ($bookavail == 1) {
                         $datesAvailable [] = array (
                                   $bookavail,
                                   $fromdate,
                                   $month,
                                   $price 
                         );
                    }
                    if ($bookavail == 2) {
                         $datesBooked [] = array (
                                   $bookavail,
                                   $fromdate,
                                   $month,
                                   $year [0] 
                         );
                    }
                    if ($bookavail == 3) {
                         $datesNotAvailable [] = array (
                                   $bookavail,
                                   $fromdate,
                                   $month,
                                   $year [0] 
                         );
                    }
               }
               $inc = $inc + 1;
          }
          
          return array (
                    $datesAvailable,
                    $datesBooked,
                    $datesNotAvailable 
          );
     }
}