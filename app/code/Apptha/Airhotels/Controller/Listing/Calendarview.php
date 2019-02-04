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
class Calendarview extends \Magento\Framework\App\Action\Action {
     
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
     public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\ResourceConnection $resource, \Magento\Catalog\Model\Product $product, \Magento\Directory\Model\CurrencyFactory $CurrencyFactory, \Apptha\Airhotels\Helper\Data $dataHelper) {
          parent::__construct ( $context );
          $this->storeManager = $storeManager;
          $this->product = $product;
          $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          $this->_currencyFactory = $CurrencyFactory;
          $this->_resource = $resource;
          $this->dataHelper = $dataHelper;
     }
     
     /**
      * Execute the result
      *
      * @return $resultPage
      */
     public function execute() {
          $htmlElementValue = '';
          /**
           * Get product id and blocked array.
           */
          $productId = $this->getRequest ()->getParam ( 'productid' );
          $dateSplit = explode ( "__", $this->getRequest ()->getParam ( 'date' ) );          
          $blockedArray = $this->objectManager->get ( 'Apptha\Airhotels\Model\Checkavail' )->getBlockdate ( $productId, $this->getRequest ()->getParam ( 'date' ) );
          $avail = $this->objectManager->get ( 'Apptha\Airhotels\Controller\Listing\Calendar' )->getDaysForAvailDays ( count ( $blockedArray [0] ), $blockedArray [0] );
          $_blockedArr = $this->objectManager->get ( 'Apptha\Airhotels\Controller\Listing\Calendar' )->getDaysForAvailDays ( count ( $blockedArray [1] ), $blockedArray [1] );
          $_blockedArrayCust = $this->objectManager->get ( 'Apptha\Airhotels\Controller\Listing\Calendar' )->getBlockdateBook ( $productId, $this->getRequest ()->getParam ( 'date' ) );
          $blocked = array_merge ( $_blockedArr, $_blockedArrayCust );
          $_sp = $_blocked = $speAvailArray = array ();
          $notAvail = $this->objectManager->get ( 'Apptha\Airhotels\Controller\Listing\Calendar' )->getDaysForAvailDays ( count ( $blockedArray [2] ), $blockedArray [2] );
          /**
           * Get special price available dates.
           */
          $specialAvail = $this->objectManager->get ( 'Apptha\Airhotels\Controller\Listing\Calendar' )->getSpecialPriceDays ( count ( $blockedArray [0] ), $blockedArray [0] );             
          foreach ( $specialAvail as $key => $value ) {
               $avail = explode ( ",", $key );
               foreach ( $avail as $_val ) {
                   $_spDay = ( int ) $_val;
                   if (!in_array($_spDay, $_blockedArrayCust)){
                   $_sp [$_spDay] = $value;
                   }
               }
          }
          $x = $dateSplit [0];
          $year = $dateSplit [1];
          if ($x == "") {
               $x = date ( "n" );
          }
          $date = strtotime ( "$year/$x/1" );
          $day = date ( "D", $date );
          $prevYear = $nextYear = $year;
          $prevMonth = intval ( $x ) - 1;
          $nextMonth = intval ( $x ) + 1;
          /**
           * If current month is December or January month then navigation links have to be updated to point to prev / next years
           */
          if ($x == 1) {
               $prevMonth = 12;
               $prevYear = $year - 1;
          }
          if ($x == 12) {
               $nextMonth = 1;
               $nextYear = $year + 1;
          }
          $htmlElementValue = $this->HtmlTable ( $prevMonth, $prevYear, $nextMonth, $nextYear, $productId, $date );
          $dayDataArray = $this->getDateArray ();
          $totaldays = date ( "t", $date );
          $st = $dayDataArray [$day];
          $tl = $this->getDaysCount ( $st, $totaldays );
          $ctr = $d = 1;
          for($i = 1; $i <= $tl; $i ++) {
               if ($ctr == 1) {
                    $htmlElementValue = $htmlElementValue . "<tr class='blockcal'>";
               }
               $arrayHtmlElement = array (
                         'i' => $i,
                         'st' => $st,
                         'd' => $d,
                         'totaldays' => $totaldays,
                         'year' => $year,
                         'x' => $x,
                         'htmlElementValue' => $htmlElementValue,
                         'date' => $date,
                         'blocked' => $blocked,
                         'speAvailArray' => $speAvailArray,
                         'notAvail' => $notAvail,
                         '_sp' => $_sp,
                         '_blocked' => $_blocked 
               );
               $htmlElementValue = $this->htmlElementCalenderView ( $arrayHtmlElement );
               if ($i >= $st && $d <= $totaldays) {
                    $d ++;
               }
               $ctr ++;
               if ($ctr > 7) {
                    $ctr = 1;
                    $htmlElementValue = $htmlElementValue . "</tr>";
               }
          }
          $htmlElementValue = $htmlElementValue . '</table>';
          $htmlElementValue = $htmlElementValue . '<input type="hidden" value="' . $x . '" id="currentMonth" />';
          $htmlElementValue = $htmlElementValue . '<input type="hidden" value="' . $year . '" id="currentYear" />';
          ob_start ();
          echo $htmlElementValue;
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
           * setting the StartDate
           */
          $sStartDate = gmdate ( "Y-m-d", strtotime ( $sStartDate ) );
          /**
           * setting the End Date
           */
          $sEndDate = gmdate ( "Y-m-d", strtotime ( $sEndDate ) );
          /**
           * Setting the startDate to Days array
           */
          $sCurrentDate = $sStartDate;
          $aDays [] = $sStartDate;
          /**
           * Iterating while loop
           */
          while ( $sCurrentDate < $sEndDate ) {
               $sCurrentDate = gmdate ( "Y-m-d", strtotime ( "+1 day", strtotime ( $sCurrentDate ) ) );
               $aDays [] = $sCurrentDate;
          }
          /**
           * Return the blocked days.
           */
          return $aDays;
     }
     
     /**
      * Special price
      */
     public function getSpecialPriceDays($count, $value) {
          $avail = array ();
          for($j = 0; $j < $count; $j ++) {
               $avail [$value [$j] [1]] = $value [$j] [3];
          }
          return $avail;
     }
     
     /**
      * Function name: HtmlTable
      * Html Table Value
      *
      * @var $prev_month
      * @var $prev_year
      * @var $next_month
      * @var $next_year
      * @var $productId
      * @var $date
      * return $htmlElementValue
      */
     public function HtmlTable($prev_month, $prev_year, $next_month, $next_year, $productId, $date) {
          $htmlElementValue = '';
          $previousTextMessage = __ ( '' );
          $nextTextMessage = __ ( '' );
          $htmlElementValue = $htmlElementValue . '<a class="pre_grid" href="javascript:void(0);" onclick="ajaxLoadCalendar(\'' . $this->getBaseUrl () . 'booking/listing/calendarview/?date=' . $prev_month . '__' . $prev_year . '&productid=' . $productId . '\')" ><i class="sprite ic-prev-calendar">' . $previousTextMessage .'</i></a>';
          $htmlElementValue = $htmlElementValue . '<div class="date_grid">' . date ( "F, Y", $date ) . '</div>';
          $htmlElementValue = $htmlElementValue . '<a class="next_grid" href="javascript:void(0);" onclick="ajaxLoadCalendar(\'' . $this->getBaseUrl () . 'booking/listing/calendarview/?date=' . $next_month . '__' . $next_year . '&productid=' . $productId . '\')" ><i class="sprite ic-next-calendar">' . $nextTextMessage . '</i></a>';
          return $htmlElementValue . "<table border = '0' cellspacing = '0'  bordercolor='blue' cellpadding ='0' class='calend airhotels_host_calender_hourly airhotels_host_calender'>
                        <thead>
                        <tr class='weekDays'>
                        <th><font size = '2' face = 'tahoma'>Sun</font></th>
                        <th><font size = '2' face = 'tahoma'>Mon</font></th>
                        <th><font size = '2' face = 'tahoma'>Tue</font></th>
                        <th><font size = '2' face = 'tahoma'>Wed</font></th>
                        <th><font size = '2' face = 'tahoma'>Thu</font></th>
                        <th><font size = '2' face = 'tahoma'>Fri</font></th>
                        <th><font size = '2' face = 'tahoma'>Sat</font></th>
                        </tr>
                        </thead> ";
     }
     
     /**
      * Function Name: tableHtmlValue
      * Setting the Html Table Value
      *
      * @return string
      */
     public function tableHtmlValue() {
          $tableHtmlValue = '';
          return $tableHtmlValue . "<table border = '0' cellspacing = '0'  bordercolor='blue' cellpadding ='2' class='calend'>
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
      * Function Name: getDaysCount
      *
      * return $t1
      */
     public function getDaysCount($st, $totaldays) {
          if (($st >= 6 && $totaldays == 31) || ($st == 7 && $totaldays == 30)) {
               $tl = 42;
          } else {
               $tl = 35;
          }
          return $tl;
     }
     /**
      * Function Name: getDateArray
      *
      * return $dateArray
      */
     public function getDateArray() {
          return array ("Sun" => 1,
                    "Mon" => 2,
                    "Tue" => 3,
                    "Wed" => 4,
                    "Thu" => 5,
                    "Fri" => 6,
                    "Sat" => 7
          );
     }
     /**
      * Function to render HTML view of calendar view action
      *
      * @param unknown $arrayHtmlElement             
      */
     public function htmlElementCalenderView($arrayHtmlElement) {
          /**
           * Assign array elements to corresponding variables
           *
           * @var unknown
           */
          $i = $arrayHtmlElement ['i'];
          $st = $arrayHtmlElement ['st'];
          $d = $arrayHtmlElement ['d'];
          $_totaldays = $arrayHtmlElement ['totaldays'];
          $year = $arrayHtmlElement ['year'];
          $x = $arrayHtmlElement ['x'];
          $date = $arrayHtmlElement ['date'];
          $htmlElementValue = $arrayHtmlElement ['htmlElementValue'];
          $_blocked = $arrayHtmlElement ['blocked'];
          $_notAvail = $arrayHtmlElement ['notAvail'];
          $_sp = $arrayHtmlElement ['_sp'];          
          /**
           * Checking the condition for total days
           */
          if ($i >= $st && $d <= $_totaldays) {
               if (strtotime ( "$year-$x-$d" ) < strtotime ( date ( "Y-n-j" ) )) {
                    $htmlElementValue = $htmlElementValue . "<td align='center' class='previous days '><font size = '2' face = 'tahoma'>$d</font></td>";
               } else {
                    $date = strtotime ( "$year/$x/$d" );
                    $tdDate = 'tdId' . '_' . date ( "m/d/Y", $date );                    
                    if (in_array ( $d, $_blocked )) {
                         $htmlElementValue = $htmlElementValue . "<td id=" . $tdDate . " class='previous days' align='center' style='background-color:#E07272;color: #f59191;'><font size = '2' face = 'tahoma'>$d</font>                         
                         </td>";
                    } else if (array_key_exists ( $d, $_sp )) {
                         $priceHelper = $this->objectManager->get('Magento\Framework\Pricing\Helper\Data');
                         $convertedPrice = $priceHelper->currency($_sp [$d], true, false);
                         $htmlElementValue = $htmlElementValue . "<td style='background-color:#6484da;' id=" . $tdDate . " class='normal days special-offer " . $d . " ' align='center' ><font size = '2' face = 'tahoma'>$d</font><br><div class='special-price' style=''>" .
                         $convertedPrice . "</div></td>";
                    } else if (in_array ( $d, $_notAvail )) {
                         $htmlElementValue = $htmlElementValue . "<td id=" . $tdDate . " class='normal days " . $d . " ' align='center'style='background-color:#ead470;color: #c5b156;' ><font size = '2' face = 'tahoma'>$d</font></td>";
                    }
                     else {
                         $htmlElementValue = $htmlElementValue . "<td id=" . $tdDate . " class='normal days " . $d . " ' align='center' ><font size = '2' face = 'tahoma'>$d</font></td>";
                    }
               }
          } else {
               $htmlElementValue = $htmlElementValue . "<td>&nbsp</td>";
          }
          return $htmlElementValue;
     }
     /**
      * Getting website base url
      */
     public function getBaseUrl() {
          $storeManager = $this->objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' );
          
          return $storeManager->getStore ()->getBaseUrl ();
     }
}
