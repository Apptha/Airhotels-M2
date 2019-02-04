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
class Blockdate extends \Magento\Framework\App\Action\Action {

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
    public function __construct(\Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Directory\Model\CurrencyFactory $CurrencyFactory,
        \Magento\Catalog\Model\Product $product,
        \Apptha\Airhotels\Helper\Data $dataHelper) {
        parent::__construct ( $context );
        $this->storeManager = $storeManager;
        $this->product = $product;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $this->_resource = $resource;
        $this->_currencyFactory = $CurrencyFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute() {


        /**
         * Get the post data such as
         * 'check_in'
         * 'check_out'
         * 'book_avail'
         * 'price_per'
         * 'hourlyDateValue'
         */
        $checkIn = $this->getRequest ()->getPost ( 'check_in' );
        $checkOut = $this->getRequest ()->getPost ( 'check_out' );
        $bookAvail = $this->getRequest ()->getPost ( 'book_avail' );
        $productId = $this->getRequest ()->getPost ( 'productid' );
        $pricePer = trim ( $this->getRequest ()->getPost ( 'price_per' ) );
        /**
         * Get the Model for 'catalog/product' and load the id
         */

            /**
             * Save blocked date by reverse order
             */
            $date = $this->startEndDate ( $checkIn, $checkOut );
            $startDate = $date ['startDate'];
            $endDate = $date ['endDate'];

        if ($bookAvail == 3) {
            $pricePer = '1';
        }
        /**
         * Get the 'airhotels/product' of all dates between Two dates
         */
        $daysData = $this->getAllDatesBetweenTwoDates ( $startDate, $endDate );
        $monthwiseArray = $this->getMonthwiseArrayData ( $daysData );
        /**
         * Iterating the loop
         */
        foreach ( $monthwiseArray as $key => $monthArr ) {
            $dateValue = array ();
            $fromDate = $toDate = '';
            $fromDate = $monthArr ['fromDate'];
            $toDate = $monthArr ['toDate'];
            $calDate = $key;
            $mY = explode ( "__", $calDate );
            $month = $mY [0];
            $year = $mY [1];
            if ($fromDate <= $toDate) {
                $date1 = strtotime ( $fromDate );
                $date2 = strtotime ( $toDate );
                /**
                 * Get all days between two dates interval
                 */
                while ( $date1 <= $date2 ) {
                    $dateValue [] = date ( "d", $date1 );
                    $date1 = $date1 + 86400;
                }
            }
            $fDate = implode ( ",", $dateValue );
            /**
             * New formate for price
             */
            $dateValueCondition = count ( $dateValue );

                for($j = 0; $j < $dateValueCondition; $j ++) {
                    $this->datePriceUpdate ( $productId, $month, $year, $dateValue [$j] );
                }

            $this->calendarDelete ( $pricePer, $dateValue, $month, $year, $fDate );
        }
        $this->calendarview();
    }

    /**
     * Function Name: startEndDate
     * Get the Start and End Date
     *
     * @param date $checkIn
     * @param date $checkOut
     * @return multitype:string
     */
    public function startEndDate($checkIn, $checkOut) {
        $date = array ();
        /**
         * Convert string to time.
         */
        if (strtotime ( $checkIn ) >= strtotime ( $checkOut )) {
            $date ['startDate'] = date ( "Y-m-d", strtotime ( $checkOut ) );
            $date ['endDate'] = date ( "Y-m-d", strtotime ( $checkIn ) );
        } else {
            $date ['startDate'] = date ( "Y-m-d", strtotime ( $checkIn ) );
            $date ['endDate'] = date ( "Y-m-d", strtotime ( $checkOut ) );
        }
        /**
         * Returnthe date
         */
        return $date;
    }

    /**
     * Get all dates between two dates interval
     *
     * @param date $eventStartDate
     * @param date $eventEndDate
     * @return array dates
     */
    public function getAllDatesBetweenTwoDates($eventStartDate, $eventEndDate) {
        $day = 86400;
        $format = 'Y-m-d';
        /**
         * Getting start date
         */
        $startTime = strtotime ( $eventStartDate );
        /**
         * Getting End date
         */
        $endTime = strtotime ( $eventEndDate );
        /**
         * calculating number of days
         */
        $numDays = round ( ($endTime - $startTime) / $day ) + 1;
        $days = array ();
        /**
         * Iterating for loop
         */
        for($i = 0; $i < $numDays; $i ++) {
            $days [] = date ( $format, ($startTime + ($i * $day)) );
        }
        /**
         * returning the days
         */
        return $days;
    }

    /**
     * Functio Name: getMonthwiseArrayData
     * Get monthwise date array
     *
     * @param array $days
     * @return array $monthwiseArray
     */
    public function getMonthwiseArrayData($days) {
        $monthwiseArray = array ();
        /**
         * Looping the Days array
         */
        foreach ( $days as $day ) {
            $monthYear = date ( "m__Y", strtotime ( $day ) );
            /**
             * check weather the monthYear Value does exist in monthWiseArray
             */
            if (array_key_exists ( $monthYear, $monthwiseArray )) {
                /**
                 * setting the Value of monthWiseArray
                 */
                $monthwiseArray [$monthYear] ['toDate'] = date ( "Y-m-d", strtotime ( $day ) );
            } else {
                /**
                 * Setting the value of monthWiseArray
                 */
                $monthwiseArray [$monthYear] ['fromDate'] = date ( "Y-m-d", strtotime ( $day ) );
                $monthwiseArray [$monthYear] ['toDate'] = date ( "Y-m-d", strtotime ( $day ) );
            }
        }
        return $monthwiseArray;
    }

    /**
     * Function Name: datePriceUpdate
     * New date price update fucntion
     *
     * @param int $productId
     * @param int $month
     * @param int $year
     * @param int $dateValue
     */
    public function datePriceUpdate($productId, $month, $year, $dateValue) {
        /**
         * Get the Colletion for calendar with such additonal filters
         * 'product_id'
         * 'month'
         * 'year'
         * 'blockfrom'
         * 'blocktime'
         */
        $collections = $this->objectManager->get('Apptha\Airhotels\Model\Calendar')->getCollection ()->addFieldToFilter ( 'product_id', $productId )->addFieldToFilter ( 'month', $month )->addFieldToFilter ( 'year', $year )->addFieldToFilter ( 'blockfrom', array (
                'like' => "%" . $dateValue . "%"
        ) );
        /**
         * Block dat string
         */
        $blockingDaysString = '';
        foreach ( $collections as $collection ) {
            /**
             * Blocked String Data
             */
            $blockedStringData = $collection->getBlockfrom ();
            /**
             * Blocked days
             */
            $blockedDays = explode ( ",", $blockedStringData );
            /**
             * Blocking days
             */
            $blockingDays = array (
                    $dateValue
            );
            /**
             * Get blocking days.
             *
             * @var $blockingDaysArray
             */
            $blockingDaysArray = array_diff ( $blockedDays, $blockingDays );
            $blockingDaysString = implode ( ",", $blockingDaysArray );
            $blockCalendartable = 'airhotels_calendar';
            $coreResource = $this->_resource;
            $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
            /**
             * Updating the table with the fields
             * 'product_id'
             * 'month'
             * 'year'
             * 'blockfrom'
             */
            $connection->update ( $coreResource->getTableName ( $blockCalendartable ), array (
                    'blockfrom' => $blockingDaysString
            ), array (
                    'product_id = ?' => $productId,
                    'month = ?' => $month,
                    'year = ?' => $year,
                    'blockfrom = ?' => $collection->getBlockfrom ()
            ) );
        }
    }

    /**
     * Function Name: calendarDelete
     * Delete the Calendar
     *
     * @param int $pricePer
     * @param int $dateValue
     * @param int $month
     * @param int $year
     * @param int $fDate
     * @param int $blockTime
     */
    public function calendarDelete($pricePer, $dateValue, $month, $year, $fDate) {
        /**
         * Get the Product Id Value.
         */
        $productId = $this->getRequest ()->getPost ( 'productid' );

        /**
         * Block Calendar Value.
         */
        $blockCalendartable = 'airhotels_calendar';
        /**
         * get the BookAvil Value.
         */
        $bookAvail = $this->getRequest ()->getPost ( 'book_avail' );
        /**
         * Get core Connetion from DB DLL
         */
        $coreResource = $this->_resource;
        /**
         * conetion Resource for Model file.
         */
        $conn = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        /**
         * Check the '$pricePer' value is not empty.
         */
        if ($pricePer != '') {

            /**
             * Delete the row from table.
             */
            $conn->delete ( $coreResource->getTableName ( $blockCalendartable ), array (
                    'product_id = ? ' => $productId,
                    'month = ? ' => $month,
                    'year = ? ' => $year,
                    'blockfrom = ?' => $fDate
            ) );
            /**
             * Check bookavail value is one.
             */

                /**
                 * Insert the Values into Table.
                 */
                $conn->insert ( $coreResource->getTableName ( $blockCalendartable ), array (
                        'product_id' => $productId,
                        'book_avail' => $bookAvail,
                        'month' => $month,
                        'year' => $year,
                        'blockfrom' => $fDate,
                        'price' => $pricePer,
                        'created' => $this->now (),
                        'updated' => $this->now ()
                ) );

        }
        /**
         * Check weather the productId is set.
         */
        if (isset ( $productId ) && isset ( $month ) && isset ( $year )) {
            $blockfromValue = '';
            $conn->delete ( $coreResource->getTableName ( $blockCalendartable ), array (
                    'product_id = ? ' => $productId,
                    'month = ? ' => $month,
                    'year = ? ' => $year,
                    'blockfrom = ? ' => $blockfromValue
            ) );
        }
    }

    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function calendarview() {


        $htmlElementValue = '';
        /**
         * Get product id.
         */
        $productId = $this->getRequest ()->getParam ( 'productid' );
        $dateSplit = explode ( "__", $this->getRequest ()->getParam ( 'date' ) );
        /**
         * Get blocked array.
         */
        $blockedArray = $this->objectManager->get('Apptha\Airhotels\Model\Checkavail')->getBlockdate( $productId, $this->getRequest ()->getParam ( 'date' ) );
        $avail = $this->objectManager->get('Apptha\Airhotels\Controller\Listing\Calendar')->getDaysForAvailDays ( count ( $blockedArray [0] ), $blockedArray [0] );
        $blockedArr = $this->objectManager->get('Apptha\Airhotels\Controller\Listing\Calendar')->getDaysForAvailDays ( count ( $blockedArray [1] ), $blockedArray [1] );
        $blockedArrayCust = $this->objectManager->get('Apptha\Airhotels\Controller\Listing\Calendar')->getBlockdateBook ( $productId, $this->getRequest ()->getParam ( 'date' ) );
        $blocked = array_merge ( $blockedArr, $blockedArrayCust );

        $_sp = $_blocked = $speAvailArray = array ();
        $notAvail = $this->objectManager->get('Apptha\Airhotels\Controller\Listing\Calendar')->getDaysForAvailDays ( count ( $blockedArray [2] ), $blockedArray [2] );
        /**
         * Get special aviail dates.
         */
        $specialAvail = $this->objectManager->get('Apptha\Airhotels\Controller\Listing\Calendar')->getSpecialPriceDays ( count ( $blockedArray [0] ), $blockedArray [0] );
        foreach ( $specialAvail as $key => $value ) {
            $avail = explode ( ",", $key );
            foreach ( $avail as $_val ) {
                $spDay = ( int ) $_val;
                $_sp [$spDay] = $value;
            }
        }
        $x = $dateSplit [0];
        if ($x == "") {
            $x = date ( "n" );
        }
        $year = $dateSplit [1];
        $date = strtotime ( "$year/$x/1" );
        $day = date ( "D", $date );
        $prevYear = $year;
        $nextYear = $year;
        $prevMonth = intval ( $x ) - 1;
        $nextMonth = intval ( $x ) + 1;
        /**
         * if current month is Decembe or January month navigation links have to be updated to point to next / prev years
         */
        if ($x == 12) {
            $nextMonth = 1;
            $nextYear = $year + 1;
        }
        if ($x == 1) {
            $prevMonth = 12;
            $prevYear = $year - 1;
        }
        $totaldays = date ( "t", $date );
        $htmlElementValue = $this->HtmlTable ( $prevMonth, $prevYear, $nextMonth, $nextYear, $productId, $date );
        $dayDataArray = $this->getDateArray ();
        $st = $dayDataArray [$day];
        $tl = $this->getDaysCount ($st,$totaldays);
        $ctr = $d = 1;
        for($i = 1; $i <= $tl; $i ++) {
            if ($ctr == 1) {
                $htmlElementValue = $htmlElementValue . "<tr class='blockcal'>";
            }
            $arrayHtmlElement = array('i'=>$i,'st'=>$st,'d'=>$d,'totaldays'=>$totaldays,'year'=>$year,'x'=>$x,'htmlElementValue'=>$htmlElementValue,'date'=>$date,'blocked'=>$blocked,'speAvailArray'=>$speAvailArray,'notAvail'=>$notAvail,'_sp'=>$_sp,'_blocked'=>$_blocked);
            $htmlElementValue = $this->htmlElementCalenderView($arrayHtmlElement);
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
     * Function Name: tableHtmlValue
     * Setting the Html Table Value
     *
     * @return string
     */
    public function tableHtmlValue() {
        /**
         * Html Table Value
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
     * Function name: HtmlTable
     * Html Table Value
     *
     * @var $prev_month
     * @var $prev_year
     * @var $next_month
     * @var $next_year
     * @var $productId
     * @var $date
     */
    public function HtmlTable($prev_month, $prev_year, $next_month, $next_year, $productId, $date) {
        $htmlElementValue = '';
        /**
         * Text message.
         */
        $nextTextMessage = __ ( 'Next' );
        $previousTextMessage = __ ( 'Previous' );
        $htmlElementValue = $htmlElementValue . '<a class="pre_grid" href="javascript:void(0);" onclick="ajaxLoadCalendar(\'' . $this->getBaseUrl () . 'booking/listing/calendarview/?date=' . $prev_month . '__' . $prev_year . '&productid=' . $productId . '\')" >' . $previousTextMessage . '</a>';
        $htmlElementValue = $htmlElementValue . '<div class="date_grid">' . date ( "F, Y", $date ) . '</div>';
        $htmlElementValue = $htmlElementValue . '<a class="next_grid" href="javascript:void(0);" onclick="ajaxLoadCalendar(\'' . $this->getBaseUrl () . 'booking/listing/calendarview/?date=' . $next_month . '__' . $next_year . '&productid=' . $productId . '\')" >' . $nextTextMessage . '</a>';
        /**
         * return $htmlElementValue
         */
        return $htmlElementValue . "<table border = '1' cellspacing = '0'  bordercolor='blue' cellpadding ='2' class='calend airhotels_host_calender_hourly airhotels_host_calender'>
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
     * Getting website base url
     */
    public function getBaseUrl()
    {
        $storeManager = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface');

        return $storeManager->getStore()->getBaseUrl();
    }
    /**
     * Function Name: getDateArray
     *
     * return $dateArray
     */
    public function getDateArray() {
        return array (
                "Sun" => 1,
                "Mon" => 2,
                "Tue" => 3,
                "Wed" => 4,
                "Thu" => 5,
                "Fri" => 6,
                "Sat" => 7
        );
    }
    /**
     * Function Name: getDaysCount
     *
     * return $t1
     */
    public function getDaysCount($st,$totaldays) {
        if (($st >= 6 && $totaldays == 31) || ($st == 7 && $totaldays == 30)) {
            $tl = 42;
        } else {
            $tl = 35;
        }
        return $tl;
    }

    /**
     * Function to render HTML view of calendar view action
     * @param unknown $arrayHtmlElement
     */
    public function htmlElementCalenderView($arrayHtmlElement){
        /**
         * Assign array elements to corresponding variables
         * @var unknown
         */
        $i = $arrayHtmlElement['i'];
        $st = $arrayHtmlElement['st'];
        $d = $arrayHtmlElement['d'];
        $totaldays = $arrayHtmlElement['totaldays'];
        $year = $arrayHtmlElement['year'];
        $x = $arrayHtmlElement['x'];
        $htmlElementValue = $arrayHtmlElement['htmlElementValue'];
        $date = $arrayHtmlElement['date'];
        $blocked = $arrayHtmlElement['blocked'];
        $notAvail = $arrayHtmlElement['notAvail'];
        $_sp = $arrayHtmlElement['_sp'];
        /**
         * Check condition for total days
         */
        if ($i >= $st && $d <= $totaldays) {
            if (strtotime ( "$year-$x-$d" ) < strtotime ( date ( "Y-n-j" ) )) {
                $htmlElementValue = $htmlElementValue . "<td align='center' class='previous days '><font size = '2' face = 'tahoma'>$d</font></td>";
            } else {
                $date = strtotime ( "$year/$x/$d" );
                $tdDate = 'tdId' . '_' . date ( "m/d/Y", $date );
                if (in_array ( $d, $notAvail )) {
                    $htmlElementValue = $htmlElementValue . "<td id=" . $tdDate . " class='normal days " . $d . " ' align='center'style='background-color:#F18200;color: black !important;' ><font size = '2' face = 'tahoma'>$d</font></td>";
                } else if (array_key_exists ( $d, $_sp )) {
                    $baseCurrencyCode = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseCurrencyCode();
                    $currentCurrencyCode = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getCurrentCurrencyCode();
                    $rateToBase = $this->_currencyFactory->create()->load($currentCurrencyCode)->getAnyRate($baseCurrencyCode);
                    $convertedPrice = $_sp [$d] * $rateToBase;
                    $htmlElementValue = $htmlElementValue . "<td style='background-color:#65AA5F;padding: 11px 23px;' id=" . $tdDate . " class='normal days " . $d . " ' align='center' ><font size = '2' face = 'tahoma'>$d</font><br><div style='width: 25px;font-size: 1.0em;text-align: right;'>" . $convertedPrice . "</div></td>";
                } else if (in_array ( $d, $blocked )) {
                    $htmlElementValue = $htmlElementValue . "<td id=" . $tdDate . " class='previous days' align='center' style='background-color:#E07272;'><font size = '2' face = 'tahoma'>$d</font></td>";
                } else {
                    $htmlElementValue = $htmlElementValue . "<td id=" . $tdDate . " class='normal days " . $d . " ' align='center' ><font size = '2' face = 'tahoma'>$d</font></td>";
                }
            }
        } else {
            $htmlElementValue = $htmlElementValue . "<td>&nbsp</td>";
        }
        /**
         * return html element value
         */
        return $htmlElementValue;
    }

    public function now(){
        return (new \DateTime())->getTimestamp();
    }
}
