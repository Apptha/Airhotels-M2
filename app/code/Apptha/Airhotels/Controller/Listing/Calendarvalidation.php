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
class Calendarvalidation extends \Magento\Framework\App\Action\Action {

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    protected $resultJsonFactory;
    protected $dataHelper;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Apptha\Airhotels\Helper\Data $dataHelper, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory) {
        parent::__construct ( $context );
        $this->storeManager = $storeManager;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $this->dataHelper = $dataHelper;
        $this->resultJsonFactory = $resultJsonFactory;
    }
    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute() {
          $productId = $this->getRequest ()->getParam ( 'productId' );
          $month = $this->getRequest ()->getParam ( 'month' );
          $year = $this->getRequest ()->getParam ( 'year' );
          $date = $datesRange = $specialPrices = array ();
          $datesRange = $this->getOrderBasedBlockDays ( $productId, $month, $year );
          $specialPrices = $this->getSpecialPriceValues ( $productId, $month, $year );
          /* Query to fetch the booked and not available dates from calendar table */
          $results = $this->objectManager->create ( 'Apptha\Airhotels\Model\Calendar' )->getCollection ()->addFieldToFilter ( 'product_id', $productId )->addFieldToFilter ( 'month', $month )->addFieldToFilter ( 'year', $year )->addFieldToFilter ( 'book_avail', array (
                    'in' => array (
                              2,
                              3
                    )
          ) );
          foreach ( $results as $result ) {
               $bokedDates = explode ( ',', $result ['blockfrom'] );
               /* Iterate through dates */
               foreach ( $bokedDates as $bookedDate ) {
                    $date [] = $bookedDate . '-' . $result ['month'] . '-' . $result ['year'];
               }
          }
          $totalBlockDatesArray = array_merge ( $datesRange, $date );
          $data = array();
          $data['bookeddates'] = $totalBlockDatesArray ;
          $data['specialprice'] = $specialPrices;
          echo json_encode($data);
     }
     /**
      * Function Name: getOrderBasedBlockDays
      * Get the booked days based on order status
      *
      * @param int $_productId
      * @param int $_month
      * @param int $_year
      * @return array
      */
     public function getOrderBasedBlockDays($_productId, $_month, $_year) {
          $productId = $_productId;
          $month = $_month;
          $year = $_year;
          $datesRange = array ();
          $dealstatus = array (
                    'processing',
                    'complete'
          );
          $range = $this->objectManager->get ( 'Apptha\Airhotels\Model\Hostorder' )->getCollection ()->addFieldtoFilter ( 'entity_id', $productId )->addFieldToSelect ( array (
                    'fromdate',
                    'todate'
          ) )->addFieldtoFilter ( 'order_status', $dealstatus );
          $rangeCount = $range->getsize ();
          if ($rangeCount > 0) {
               foreach ( $range as $rangeVal ) {
                    $dateArr = $this->getDaysBlock ( $rangeVal ['fromdate'], $rangeVal ['todate'] );
                    foreach ( $dateArr as $dateArrVal ) {
                         /**
                          * Get Data Array Value.
                          */
                         $getDateArr = explode ( '-', $dateArrVal );
                         if ($getDateArr [0] == $year && $getDateArr [1] == $month) {
                              $datesRange [] = $getDateArr [2] . '-' . intval ( $getDateArr [1] ) . '-' . $getDateArr [0];
                         }
                    }
               }
          }
          /**
           * Return Order based booked days .
           */
          return $datesRange;
     }
     /**
      * Function Name: getDaysBlock
      * Get the days
      *
      * @param date $_startDate
      * @param date $_endDate
      * @return string
      */
     public function getDaysBlock($_startDate, $_endDate) {
          $_startDate = gmdate ( "Y-m-d", strtotime ( $_startDate ) );
          $_endDate = gmdate ( "Y-m-d", strtotime ( $_endDate ) );
          /**
           * Setting the startDate to Days array
           */
          $_Days [] = $_startDate;
          $_currentDate = $_startDate;

          while ( $_currentDate < $_endDate ) {
               $_currentDate = gmdate ( "Y-m-d", strtotime ( "+1 day", strtotime ( $_currentDate ) ) );
               $_Days [] = $_currentDate;
          }
          /**
           * Return the blocked days .
           */
          return $_Days;
     }
     /**
      * Function Name: getOrderBasedBlockDays
      * Get the booked days based on order status
      *
      * @param int $_productId
      * @param int $_month
      * @param int $_year
      * @return array
      */
     public function getSpecialPriceValues($_productId, $_month, $_year) {
          $productId = $_productId;
          $month = $_month;
          $year = $_year;
          $_sp  = array ();
         // Instance of Pricing Helper
          $priceHelper = $this->objectManager->get('Magento\Framework\Pricing\Helper\Data'); 
          $blockedArray = $this->objectManager->get ( 'Apptha\Airhotels\Model\Checkavail' )->getBlockdate ( $productId, $month, $year);
          $avail = $this->objectManager->get ( 'Apptha\Airhotels\Controller\Listing\Calendar' )->getDaysForAvailDays ( count ( $blockedArray [0] ), $blockedArray [0] );
          $_blockedArrayCust = $this->objectManager->get ( 'Apptha\Airhotels\Controller\Listing\Calendar' )->getBlockdateBook ( $productId, $this->getRequest ()->getParam ( 'date' ) );
          /**
           * Get special price available dates.
           */
          $specialAvail = $this->objectManager->get ( 'Apptha\Airhotels\Controller\Listing\Calendar' )->getSpecialPriceDays ( count ( $blockedArray [0] ), $blockedArray [0] );
          foreach ( $specialAvail as $key => $value ) {
               $avail = explode ( ",", $key );
               foreach ( $avail as $_val ) {
                   $_spDay = ( int ) $_val;
                   if (!in_array($_spDay, $_blockedArrayCust)){
                   $_sp [$_spDay] = $priceHelper->currency($value, true, false);
                   }
               }
          }
           return $_sp;
     }
}
