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
 * Airhotels listing model
 */
class Checkavail extends \Magento\Framework\DataObject{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Framework\App\Config\ValueInterface
     */
    protected $_backendModel;
    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;
    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_configValueFactory;
    /**
     * @var int $_storeId
     */
    protected $_storeId;
    /**
     * @var string $_storeCode
     */
    protected $_storeCode;
    /**
     * @var string $request
     */
    protected $request;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
     * @param \Magento\Framework\App\Config\ValueInterface $backendModel,
     * @param \Magento\Framework\DB\Transaction $transaction,
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory,
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\App\Config\ValueInterface $backendModel,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResult,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_backendModel = $backendModel;
        $this->product = $product;
        $this->_transaction = $transaction;
        $this->_resource = $resource;
        $this->jsonResult = $jsonResult;
        $this->_configValueFactory = $configValueFactory;
        $this->_storeId=(int)$this->_storeManager->getStore()->getId();
        $this->_storeCode=$this->_storeManager->getStore()->getCode();
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->request = $request;
    }

/**
     * Function Name: CheckAvail
     * Checkavail function is used to check the available dates for booking.
     */
    public function checkavailable($listingParameters) {
        $from = $listingParameters['from'];
        $to = $listingParameters['to'];
        $productid = $listingParameters['productid'];
        $price = $listingParameters['price'];
        $to = $this->getToDateValue ( $to );
        $calendarDate = $this->dateVerfiy ( $productid, $from, $to );
        $bookedDates = $this->calendarDateRange ($productid, $from, $to);
        $avPrice = $this->getSpecialPrice ( $calendarDate );
        $days = $av = array ();
        $connection  = $this->_resource->getConnection();
        $orderItemTable = $connection->getTableName('sales_order');
        $dealstatus [0] = "processing";
        $dealstatus [1] = "complete";
        $range = $this->dateRangeArray ( $orderItemTable, $productid, $dealstatus );
        $count = count ( $range );
        $day = 86400;
        $Incr = 0;
        $startStrTime = strtotime ( $from );
        $endStrTime = strtotime ( $to );
        $numDayRound = round ( ($endStrTime - $startStrTime) / $day ) + 1;
            for($i = 0; $i <= $count - 1; $i ++) {
                $fromdateValue = $range [$i] ['fromdate'];
                $todateValue = $range [$i] ['todate'];
                $startValue = $fromdateValue;
                $endValue = $todateValue;
                $startTime = strtotime ( $startValue );
                $endTime = strtotime ( $endValue );
                $numDays = round ( ($endTime - $startTime) / $day ) + 1;
                for($d = 0; $d < $numDays; $d ++) {
                    $days [] = date ( 'm/d/Y', ($startTime + ($d * $day)) );
                }
            }
        $Incr = $this->objectManager->get('Apptha\Airhotels\Model\Booking')->dayWiseBooked ( $Incr, $numDayRound, $startStrTime, $days, $day, $productid );
        $total = 0;
        $pFrom = strtotime ( $from );
        $pTo = strtotime ( $to );
        $pDay = round ( ($pTo - $pFrom) / $day ) + 1; 
        $productInformation = $this->product->load($productid);
        $propertyMaximum = $productInformation->getMaximumDays();
        $propertyMinimum = $productInformation->getMinimumDays();
        //check host product
        $customerSession = $this->objectManager->get ( 'Magento\Customer\Model\Session' );
        if($customerSession->isLoggedIn () && $productInformation->getUserId()==$customerSession->getCustomerId()){
                 echo __("Host can't book their own listing" );
        }
        elseif ($Incr == 0) {
            $overallTotalHours = 0;
            $totalOverNightFee = 0;
                for($pr = 0; $pr < $pDay; $pr ++) {
                    $pin = date ( 'd', ($pFrom + ($pr * $day)) );
                    $pIn = ( int ) $pin;
                    $month = date ( 'n', ($pFrom + ($pr * $day)) );
                    $av [$month] [$pIn] = $price;
                }
                     if ($propertyMinimum > $pDay) {                        
                        /**
                         * Getting Minimum working days of a property
                         */
                        echo  __('Minimum property day(s) which is ') . $propertyMinimum;
                        return;
                    }
                    if ($propertyMaximum < $pDay) {                        
                        /**
                         * Getting maximum working days for a property
                         */
                        echo  __('Maximum property day(s) which is ') . $propertyMaximum;
                        return;
                    }
            $total = $this->setTotalFromAv ( $av, $avPrice);
            $this->objectManager->get('\Magento\Checkout\Model\Session')->setRemoveIncludedOvernightFeeInCart ( 0 );
            $subtotalValue = $total;
            $processingFee = $this->objectManager->get('\Apptha\Airhotels\Helper\Data')->getServiceFee();
            $serviceFee = round ( ($subtotalValue / 100) * ($processingFee), 2 );
            $getAvailableData = array (
                    'servicefee' => $serviceFee,
                    'subtotal' => $subtotalValue,
                    'pday' => $pDay,
                    'totalhours' => $overallTotalHours,
                    'totalovernightfee' => $totalOverNightFee,
                    'productid' => $productid
            );
            $this->objectManager->get('Apptha\Airhotels\Model\Booking')->getAvailDates ( $getAvailableData );
        } else {
            $this->checkAvailableDatas($propertyMinimum, $pDay, $propertyMaximum, $pFrom, $pTo, $bookedDates);
        }
    }
    
    /**
     * Function Name: CheckAvailDates
     * Checkavail function is used to check the available dates for booking.
     */
    public function checkAvailableDatas($propertyMinimum, $pDay, $propertyMaximum, $pFrom, $pTo, $bookedDates) {
         $unavaiableDaysCount = 0;
         $startDate = $pFrom;
         $endDate = $pTo;
         $bookedDateRange = $bookedDates;
       // Check that unavaiable dates is between start & end date
       foreach($bookedDateRange AS $bookedDateRanges) {
         $timestamp = strtotime($bookedDateRanges);
         if($timestamp >= $startDate && $timestamp <= $endDate) {
             $unavaiableDaysCount++;
         } 
     }
        if ($propertyMinimum > $pDay && $unavaiableDaysCount==0 ) {
            /**
             * Getting Minimum working days of a property
             */
            echo __('Minimum property day(s) which is ').$propertyMinimum;
            return;
        }
        if ($propertyMaximum < $pDay && $unavaiableDaysCount==0) {             
            /**
             * Getting maximum working days for a property
             */
            echo __('Maximum property day(s) which is ').$propertyMaximum;
            return;
        }
        echo __('Dates are not available refer to calendar');
        
    }


    /**
     * Function Name: getToDateValue
     * Get to Date
     *
     * @param date $to
     * @return string
     */
    public function getToDateValue($to) {
            /**
             * convert the $to Date Vlaue.
             */
            $to = strtotime ( $to );
            $todate = strtotime ( '-1 day', $to );
        /**
         * Return the To date Vlaue.
         */
        return date ( 'm/d/Y', $todate );
    }

    /**
     * Function Name: dateVerfiy
     *
     * @param int $productid
     * @param date $from
     * @param date $to
     * @return array $calendar
     */
    public function dateVerfiy($productid, $from, $to) {
        /**
         * Product id Value.
         */
        $productId = $productid;
        $From = date ( 'Y-n', strtotime ( $from ) );
        $To = date ( 'Y-n', strtotime ( $to ) );
        $dateFrom = explode ( "-", $From );
        $dateTo = explode ( "-", $To );
        $month = array_unique ( array (
                $dateFrom [1],
                $dateTo [1]
        ) );
        $year = array_unique ( array (
                $dateFrom [0],
                $dateTo [0]
        ) );
        return $this->getBlockdate ( $productId, $month, $year);
    }

    /**
     * Function Name: getBlockdate
     * Get day wise booked array by product id
     *
     * @param int $productid
     * @param date $date
     * @param date $to
     * @return array $datesRange
     */
    public function getBlockdate($productid, $date, $to = NULL) {
        if (!empty($this->request->getParam('date'))) {
            /** * Split the Date */
            $dateSplitParams = explode ( "__",$this->request->getParam('date'));
            /** * set $dateSplitParams Value to 'year' */
            $year = $dateSplitParams [1];

            /** * Set $dateSplitParams Value to 'X' */
            $x = $dateSplitParams [0];
        } else {
            $x = $date; 
            $year = $to;
        }
       $datesBooked = $datesAvailable = $datesNotAvailable = array ();
        /**
         * Check whether date set
         */
        /** * Check weather date set */

        /**
         * Get daywise blocked details
         */
        $result = $this->objectManager->get('Apptha\Airhotels\Model\Calendar')->getCollection ()
        ->addFieldToFilter ( 'month', $x )
        ->addFieldToFilter ( 'year', $year )
        ->addFieldToFilter ( 'product_id', $productid );
        /**
         * Iterating the loop
         */
        foreach ( $result as $res ) {
            /**
             * Setting the $res Vales to $bookavailVal
             */
            $bookavailVal = $res ['book_avail'];
            /**
             * assign Values to '$fromdateVal'
             */
            $fromdateVal = $res ['blockfrom'];
            /**
             * assign Values to '$monthVal'
             */
            $monthVal = $res ['month'];
            /**
             * assign Values to '$priceVal'
             */
            $priceVal = $res ['price'];
            /**
             * Check weather the Value has equal to one.
             */
            if ($bookavailVal == 1) {
                /**
                 * Assign the $datesAvailable array.
                 */
                $datesAvailable [] = array (
                        $bookavailVal,
                        $fromdateVal,
                        $monthVal,
                        $priceVal
                );
            }
            /**
             * Check the '$bookavailVal' value
             */
            if ($bookavailVal == 2) {
                /**
                 * Assign the $datesBooked array.
                 */
                $datesBooked [] = array (
                        $bookavailVal,
                        $fromdateVal,
                        $monthVal,
                        $year [0],
                        $priceVal
                );
            }
            /**
             * Check the 'bookavailVal' having three
             */
            if ($bookavailVal == 3) {
                /**
                 * Assign the $datesNotAvailable array.
                 */
                $datesNotAvailable [] = array (
                        $bookavailVal,
                        $fromdateVal,
                        $monthVal,
                        $year [0]
                );
            }
        }
        return array (
                $datesAvailable,
                $datesBooked,
                $datesNotAvailable
        );
    }
    /**
     * Function Name: getSpecialPrice
     * Get the special Price
     *
     * @param string $calendarDate
     * @return multitype
     */
    public function getSpecialPrice($calendarDate) {
        /**
         * init an empty array.
         */
        $availPrice = array ();
        $avPrice = array ();
        /**
         * Iterating foreach loop
         */
        foreach ( $calendarDate as $avail ) {
            $available = $avail;
            foreach ( $available as $availPrice ) {
                $availMonth = $availPrice [2];
                $availDays = explode ( ",", $availPrice [1] );
                $availDaysCount = $this->getArraySize ( $availDays );
                /**
                 * Iterating for loop
                 */
                for($availDay = 0; $availDay < $availDaysCount; $availDay ++) {
                    $spDay = ( int ) $availDays [$availDay];
                    $avPrice [$availMonth] [$spDay] = $availPrice [3];
                }
                /**
                 * Set avilable day as zero.
                 */
                $availDay = 0;
            }
            break;
        }
        /**
         * Return the $availPrice value.
         */
        return $avPrice;
    }

    /**
     * Function Name: getArraySize
     * Get the array size
     *
     * @param array $arrayData
     * @return number
     */
    public function getArraySize($arrayData) {
        return count ( $arrayData );
    }

    /**
     * Fucntion date range array
     * @param unknown $orderItemTable
     * @param unknown $productid
     * @param unknown $dealstatus
     * @return range[]
     */
    public function dateRangeArray($orderItemTable,$productid,$dealstatus){
        $range = array();
        /**
         * get collections from airhotels table
         * @var unknown
         */
        $dateRange = $this->objectManager->get('Apptha\Airhotels\Model\Hostorder')->getCollection ()->addFieldToSelect ( array (
                'entity_id',
                'fromdate',
                'todate',
                'order_id',
                'order_item_id'
        ) )->addFieldToFilter ( 'order_status', array (
                'eq' => 'complete'
        ) );
        /**
         * add field to filter for order status
         * as 1
         */
        /**
         * join two tables as sales_flat_order
         * and the previous collection
         */
        $dateRange->getSelect ()->join ( array (
                'sales_order' => $orderItemTable
        ), "(sales_order.entity_id = main_table.order_item_id AND main_table.entity_id = $productid  AND (sales_order.status='$dealstatus[1]' OR sales_order.status='$dealstatus[0]'))", array () );
        foreach ( $dateRange as $dateRan ) {
            $range [] = $dateRan;
        }
        /**
         * get the ranges from the collections
         */
        /**
         * return the range array from collections
         */
        return $range;
    }

    /**
     * Fucntion to set the totat
     * for the average value
     * @param unknown $av
     * @param unknown $avPrice
     * @param unknown $hourlyEnabledOrNot
     * @param unknown $propertyTimeData
     * @param unknown $propertyTime
     * @param unknown $dayCountForOvernightFee
     * @return number
     */
    public function setTotalFromAv($av, $avPrice){
        /**
         * Declare total as 0
         * @var unknown
         */
        $total = 0;
        /**
         * foreach the av array
         */
        foreach ( $av as $key => $av1 ) {
            /**
             * foreach the av1 array
             */
            foreach ( $av1 as $avkey => $av2 ) {
                if (! empty ( $avPrice [$key] [$avkey] )) {
                    /**
                     * set total if condition satisfies
                     * @var unknown
                     */
                    $total = $total + $avPrice [$key] [$avkey];
                } else {
                    /**
                     * set total if condition not satisfies
                     * @var unknown
                     */
                    $total = $total + $av [$key] [$avkey];
                }
            }
        }
        return $total;
    }

 
function calendarDateRange ($productid, $from, $to) {
        $productId = $productid;
        $datesArr = array();
        $From = date ( 'Y-n', strtotime ( $from ) );
        $dateFrom = explode ( "-", $From );
        $To = date ( 'Y-n', strtotime ( $to ) );        
        $dateTo = explode ( "-", $To );
        $month = array_unique ( array ($dateFrom [1], $dateTo [1] ) );
        $year = array_unique ( array ( $dateFrom [0], $dateTo [0] ) );
    /* Query to fetch the booked and not available dates from calendar table */
         $results = $this->objectManager->get('Apptha\Airhotels\Model\Calendar')->getCollection ()->addFieldToFilter ( 'product_id', $productId )->addFieldToFilter ( 'month', $month )->addFieldToFilter ( 'year', $year )
         ->addFieldToFilter ( 'book_avail', array('in' => array(2, 3)) );
         foreach ( $results as $result ) {
              $bokedDates = explode(',', $result['blockfrom']);
              /* Iterate through dates */
              foreach ( $bokedDates as $bookedDate ) {
                   $datesArr[] =  $bookedDate. '-' . $result ['month'] . '-' . $result ['year'];
              }
         }
         return $datesArr;
}

}
