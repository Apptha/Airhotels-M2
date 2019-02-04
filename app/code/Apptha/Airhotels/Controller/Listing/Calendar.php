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
namespace Apptha\Airhotels\Controller\Listing;

use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * This class contains loading category functions
 */
class Calendar extends \Magento\Framework\App\Action\Action {
      
     /**
      *
      * @var \Magento\Store\Model\StoreManagerInterface
      */
     protected $storeManager;
      
     /**
      *
      * @var CategoryRepositoryInterface
      */
     protected $categoryRepository;
     protected $dataHelper;
      
     /**
      * Constructor
      *
      * @param \Magento\Framework\App\Action\Context $context
      * @param \Magento\Store\Model\StoreManagerInterface $storeManager
      * @param CategoryRepositoryInterface $categoryRepository
      */
     public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\ResourceConnection $resource, \Magento\Catalog\Model\Product $product, \Apptha\Airhotels\Helper\Data $dataHelper) {
          parent::__construct ( $context );
          $this->storeManager = $storeManager;
          $this->product = $product;
          $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          $this->_resource = $resource;
          $this->dataHelper = $dataHelper;
     }
      
     /**
      * Execute the result
      *
      * @return $resultPage
      */
     public function execute() {
          $tableHtmlValue = '';

          /**
           * Get product id.
           */
          $productId = $this->getRequest ()->getParam ( 'productid' );
          /**
           * Explode the date Value
           */
          $dateSplit = explode ( "__", $this->getRequest ()->getParam ( 'date' ) );

          $propertyDateValue = $this->getRequest ()->getParam ( 'date' );
          /**
           * Get blocked date array.
           */
          $blockedArray = $this->objectManager->get ( 'Apptha\Airhotels\Model\Checkavail' )->getBlockdate ( $productId, $propertyDateValue );
          /**
           * Assign availiable, blocked and not availiable date
           */
          $blockedArr = $this->getDaysForAvailDays ( count ( $blockedArray [1] ), $blockedArray [1] );
          $blockedArrayCust = $this->getBlockdateBook ( $productId, $propertyDateValue );
          /**
           * Get bloacked dates for property.
           */
          $blocked = array_merge ( $blockedArr, $blockedArrayCust );
          /**
           * get Not Available Days
           */
          $not_available = $this->getDaysForAvailDays ( count ( $blockedArray [2] ), $blockedArray [2] );
          $specialAvailable = $this->getSpecialPriceDays ( count ( $blockedArray [0] ), $blockedArray [0] );
          $_spl = array ();
          foreach ( $specialAvailable as $key => $value ) {
               $available = explode ( ",", $key );
               foreach ( $available as $_val ) {
                    $spDay = ( int ) $_val;
                    $_spl [$spDay] = $value;
               }
          }
          $partiallyBookedview = '';
          $partiallyBookedarray = array ();
          /**
           * Get houly property or not
           */
          $speAvailArray = array ();
          /**
           * Split dates.
           */
          $x = $dateSplit [0];
          if ($x == "") {
               $x = date ( "n" );
          }
          $yearVal = $dateSplit [1];
          $dateVal = strtotime ( "$yearVal/$x/1" );
          $dayVal = date ( "D", $dateVal );
          $prevYearVal = $yearVal;
          $nextYearVal = $yearVal;
          $prevMonthVal = intval ( $x ) - 1;
          $nextMonthVal = intval ( $x ) + 1;
          /**
           * if current month is December or January month navigation links have to be updated to point to next / prev years
           */
          if ($x == 12) {
               $nextMonthVal = 1;
               $nextYearVal = $yearVal + 1;
          }
          if ($x == 1) {
               $prevMonthVal = 12;
               $prevYearVal = $yearVal - 1;
          }
          $totaldays = date ( "t", $dateVal );
          /**
           * Get the table Value
           */
          $tableHtmlValue = $this->tableHtmlValue ();
          /**
           * Initialize custom array.
           */
          $customArray = array (
                    'day' => $dayVal,
                    'totaldays' => $totaldays,
                    'x' => $x,
                    'year' => $yearVal,
                    'notAvail' => $not_available,
                    'pyear'=> $prevYearVal,
                    'nyear'=>$nextYearVal,
                    'pmonth'=>$prevMonthVal,
                    'nmonth'=>$nextMonthVal
          );
          /**
           * Get calender html style.
           */
          $tableHtmlValue = $this->calendarStyle ( $speAvailArray, $blocked, $partiallyBookedarray, $tableHtmlValue, $_spl, $partiallyBookedview, $customArray );
          $tableHtmlValue = $tableHtmlValue . '</table>';
          $this->getResponse ()->setBody ( $tableHtmlValue );
     }
      
     /**
      * Function Name: getDaysForAvailDays
      * Get Days for Controller
      *
      * @param int $count
      * @param int $value
      * @return multitype:
      */
     public function getDaysForAvailDays($count, $value) {
          $availDay = array ();
          for($j = 0; $j < $count; $j ++) {
               $availDay [] = $value [$j] [1];
          }
          return explode ( ",", implode ( ",", $availDay ) );
     }
      
     /**
      * Function Name : getBlockDateBook
      * Get the blocked date values
      */
     public function getBlockdateBook($productid, $date, $to = NULL) {
         $_productid = $productid;
          /**
           * Initalise the '$datesRange'
           */
          $datesRange = array ();
          /**
           * Dealstatus array with
           * 'processing'
           * 'complete'
           */
          $dealstatus = array (
                    'processing',
                    'complete'
          );
          /**
           * Check Whether the date is set
           */
          if ($this->getRequest ()->getParam ( 'date' )) {
               /**
                * DateSplit Value.
                */
               $dateSplit = explode ( "__", $this->getRequest ()->getParam ( 'date' ) );
               $x = array (
                         $dateSplit [0]
               );
               $year = array (
                         $dateSplit [1]
               );
          } else {
               $x = $date;
               $year = $to;
          }
          /**
           * Get the colletion value for 'airhotels_hostorder'
           * 'fromdate'
           * 'todate'
           */
          $range = $this->objectManager->get ( 'Apptha\Airhotels\Model\Hostorder' )->getCollection ()->addFieldToSelect ( array (
                    'fromdate',
                    'todate'
          ) )
          ->addFieldtoFilter ( 'order_status', $dealstatus )
          ->addFieldtoFilter ( 'entity_id', $_productid );
          $rangeCount = $range->getsize ();
          if ($rangeCount > 0) {
               foreach ( $range as $rangeVal ) {
                    /**
                     * Get Collection value for 'airhotels/product'
                     */
                    $dateArr = $this->getDaysBlock ( $rangeVal ['fromdate'], $rangeVal ['todate'] );
                    /**
                     * Itearting the Loop Value.
                     */
                    foreach ( $dateArr as $dateArrVal ) {
                         /**
                          * Get Data Array Value.
                          */
                         $getDateArr = explode ( '-', $dateArrVal );
                         if ($getDateArr [0] == $year [0] && $getDateArr [1] == $x [0]) {
                              $datesRange [] = $getDateArr [2];
                         }
                    }
               }
          }
          return $datesRange;
     }
      
     /**
      * Function Name: getDaysBlock
      * Get the days
      *
      * @param date $sStartDate
      * @param date $sEndDate
      * @return string
      */
     public function getDaysBlock($sStartDate, $sEndDate) {
          /**
           * setting the StartDate and EndDate
           */
          $sStartDate = gmdate ( "Y-m-d", strtotime ( $sStartDate ) );
          $sEndDate = gmdate ( "Y-m-d", strtotime ( $sEndDate ) );
          /**
           * Setting the startDate to Days array
           */
          $aDays [] = $sStartDate;
          $sCurrentDate = $sStartDate;
          /**
           * Iterating while loop
           */
          while ( $sCurrentDate < $sEndDate ) {
               $sCurrentDate = gmdate ( "Y-m-d", strtotime ( "+1 day", strtotime ( $sCurrentDate ) ) );
               $aDays [] = $sCurrentDate;
          }
          /**
           * Return days blocked.
           */
          return $aDays;
     }
      
     /**
      * Function Name: tableHtmlValue
      * Setting the Html Table Values
      *
      * @return string
      */
     public function tableHtmlValue() {
          /**
           * Html Table Values
           */
          $tableHtmlValue = '';
          return $tableHtmlValue . "<table border = '1' cellspacing = '0'  bordercolor='blue' cellpadding ='2' class='calend'>
                        <tr class='weekDays'>
                        <th><font size = '2' face = 'tahoma'>Sun</font></th>
                        <th><font size = '2' face = 'tahoma'>Mon</font></th>
                        <th><font size = '2' face = 'tahoma'>Tue</font></th>
                        <th><font size = '2' face = 'tahoma'>Wed</font></th>
                        <th><font size = '2' face = 'tahoma'>Thu</font></th>
                        <th><font size = '2' face = 'tahoma'>Fri</font></th>
                        <th><font size = '2' face = 'tahoma'>Sat</font></th>
                        </tr> ";
     }
      
     /**
      * Special price for days
      */
     public function getSpecialPriceDays($count, $value) {
          $avail = array ();
          for($j = 0; $j < $count; $j ++) {
               $avail [$value [$j] [1]] = $value [$j] [3];
          }
          return $avail;
     }
      
     /**
      * Function Name: calendarStyle
      * get style for blocked, Available dates
      *
      * @param string $d
      * @param array $speAvailArray
      * @param array $blocked
      * @return string
      */
     public function calendarStyle($speAvailArray, $blocked, $partiallyBookedArray, $tableHtmlValue, $_sp, $partiallyBookedView, $customArray) {
          /**
           * Extracting the customArray
           */
          $day = $customArray ['day'];
          $totaldays = $customArray ['totaldays'];
          $x = $customArray ['x'];
          $year = $customArray ['year'];
          $notAvail = $customArray ['notAvail'];
          $dayArray = array (
                    "Sun",
                    "Mon",
                    "Tue",
                    "Wed",
                    "Thu",
                    "Fri",
                    "Sat"
          );
          $styleData = $this->calendarStyleDayData ( $day, $dayArray );
          $tl = $this->getTL ( $styleData, $totaldays );
          $ctr = 1;
          $d = 1;
          for($i = 1; $i <= $tl; $i ++) {
                
               if ($ctr == 1) {
                    $tableHtmlValue = $tableHtmlValue . "<tr class='blockcal'>";
               }
               if ($i >= $styleData && $d <= $totaldays) {
                    if (strtotime ( "$year-$x-$d" ) < strtotime ( date ( "Y-n-j" ) )) {
                         $tableHtmlValue = $tableHtmlValue . "<td align='center' class='previous days '><font size = '2' face = 'tahoma'>$d</font></td>";
                    } else {
                         /**
                          * Store calender dates and data
                          * 'year','x','d','tableHtmlValue'
                          * 'partiallyBookedView','sp','not_avail'
                          * 'speAvailArray','blocked','partiallyBookedArray','propertyTime'
                          * 'propertyTimeData','hourlyEnabledOrNot'
                          */
                         $calendarDatesData = array (
                                   'year' => $year,
                                   'x' => $x,
                                   'd' => $d,
                                   'tableHtmlValue' => $tableHtmlValue,
                                   'partiallyBookedView' => $partiallyBookedView,
                                   'sp' => $_sp,
                                   'not_avail' => $notAvail,
                                   'speAvailArray' => $speAvailArray,
                                   'blocked' => $blocked,
                                   'partiallyBookedArray' => $partiallyBookedArray
                         );
                         $tableHtmlValue = $this->getCalendarDates ( $calendarDatesData );
                    }
                    $d ++;
               } else {
                    $tableHtmlValue = $tableHtmlValue . "<td>&nbsp</td>";
               }
               $ctr ++;
               if ($ctr > 7) {
                    $ctr = 1;
                    $tableHtmlValue = $tableHtmlValue . "</tr>";
               }
          }
          /**
           * Returning the table html Value
           */
          return $tableHtmlValue;
     }
      
     /**
      * Function Name: getCalendarDates
      *
      * @param int $styleData
      * @param int $totaldays
      * @return number
      */
     public function getCalendarDates($calendarDatesData) {
     
          $year = $calendarDatesData ['year'];
          $x = $calendarDatesData ['x'];
          $d = $calendarDatesData ['d'];
          $tableHtmlValue = $calendarDatesData ['tableHtmlValue'];
          $_sp = $calendarDatesData ['sp'];
          $notAvail = $calendarDatesData ['not_avail'];
          $blocked = $calendarDatesData ['blocked'];
          $date = strtotime ( "$year/$x/$d" );
          $tdDate = 'tdId' . '_' . date ( "m/d/Y", $date );
          if (in_array ( "$d", $blocked )) {
               $tableHtmlValue = $tableHtmlValue . "<td id=" . $tdDate . " class='normal customer days " . $d . " ' align='center' style='background-color:#E07272;'><font size = '2' face = 'tahoma'>$d</font></td>";
          } else if (in_array ( "$d", $notAvail )) {
               $tableHtmlValue = $tableHtmlValue . "<td id=" . $tdDate . " class='normal customer days " . $d . " ' align='center'style='background-color:#F18200;color: black !important;' ><font size = '2' face = 'tahoma'>$d</font></td>";
          } else if (array_key_exists ( $d, $_sp )) {
               $tableHtmlValue = $tableHtmlValue . "<td style='background-color:#65AA5F;padding: 11px 23px;' id=" . $tdDate . " class='normal customer days " . $d . " ' align='center' ><font size = '2' face = 'tahoma'>$d</font><br><div style='width: 25px;font-size: 1.0em;text-align: right;'>" . $this->objectManager->get ( 'Apptha\Airhotels\Block\Booking\Form' )->getCurrentCurrencySymbol () . $this->objectManager->get ( 'Apptha\Airhotels\Block\Booking\View' )->convertPrice ( $_sp [$d] ) . "</div></td>";
          } else {
               $tableHtmlValue = $tableHtmlValue . "<td id=" . $tdDate . " class='normal customer days " . $d . " ' align='center' ><font size = '2' face = 'tahoma'>$d</font></td>";
          }
          return $tableHtmlValue;
     }
      
     /**
      * Function Name: calendarStyleDayData
      *
      * calendar Style Day Data
      *
      * @param string $day
      * @param Array $dayArray
      * @return array
      */
     public function calendarStyleDayData($day, $dayArray) {
          /**
           * DayDataArray
           */
          $dayDataArray = array (
                    "Sun" => 1,
                    "Mon" => 2,
                    "Tue" => 3,
                    "Wed" => 4,
                    "Thu" => 5,
                    "Fri" => 6,
                    "Sat" => 7
          );
          /**
           * check the day array data is available in $day
           */
          if (in_array ( $day, $dayArray )) {
               return $dayDataArray [$day];
          }
     }
     /**
      * * Function Name: getTL * * @param int $styleData * @param int $totaldays * @return number
      */
     public function getTL($styleData, $totalDays) {
          if (($styleData >= 6 && $totalDays == 31) || ($styleData == 7 && $totalDays == 30)) {
               $total = 42;
          } else {
               $total = 35;
          }
          return $total;
     }
}
