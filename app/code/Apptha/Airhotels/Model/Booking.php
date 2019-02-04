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
 * Airhotels booking model
 */
class Booking extends \Magento\Framework\DataObject{

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
        \Magento\Framework\App\Config\ValueInterface $backendModel,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResult,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_backendModel = $backendModel;
        $this->_transaction = $transaction;
        $this->_resource = $resource;
        $this->jsonResult = $jsonResult;
        $this->checkoutSession = $checkoutSession;
        $this->_configValueFactory = $configValueFactory;
        $this->_storeId=(int)$this->_storeManager->getStore()->getId();
        $this->_storeCode=$this->_storeManager->getStore()->getCode();
        $this->product = $product;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }


    /**
     * Function Name: dayWiseBooked
     * get Day wise booked
     *
     * @param int $Incr
     * @param date $numDay1
     * @param date $sTime1
     * @param date $days
     */
    public function dayWiseBooked($Incr, $numDay1, $sTime1, $days, $day, $productid) {
        $preMonth = $preYear = '';
        if ($Incr == 0) {
            /**
             * Itearting Loop
             */
            for($d1 = 0; $d1 < $numDay1; $d1 ++) {
                $daysIn = date ( 'm/d/Y', ($sTime1 + ($d1 * $day)) );
                $dIn = date ( 'd', ($sTime1 + ($d1 * $day)) );
                $currentMonth = date ( 'm', ($sTime1 + ($d1 * $day)) );
                $currentYear = date ( 'Y', ($sTime1 + ($d1 * $day)) );
                /**
                 * Check block and not available dates based on month and year
                 */
                if ($preMonth != $currentMonth || $preYear != $currentYear) {
                    $calendarDate = $booked = $booked = $notAvail = array ();
                    /**
                     * Getting booked and blocked days
                     */
                    $calendarDate = $this->objectManager->get('Apptha\Airhotels\Model\Checkavail')->getBlockdate ( $productid, $currentMonth, $currentYear );
                    $booked = $this->getDays ( count ( $calendarDate [1] ), $calendarDate [1] );
                    $notAvail = $this->getDays ( count ( $calendarDate [2] ), $calendarDate [2] );
                    $booked = array_unique ( $booked );
                    $notAvail = array_unique ( $notAvail );
                    $preMonth = $currentMonth;
                    $preYear = $currentYear;
                }
                /**
                 * Checking whether its available in calendar ornot
                 */

                if (in_array ( $daysIn, $days ) || in_array ( $dIn, $booked ) || in_array ( $dIn, $notAvail )) {
                    $Incr = 1 + $Incr;
                    break;
                }
            }
        }
        return $Incr;
    }

    /**
     * get Days Value
     *
     * @param int $count
     * @param array $value
     * @return array $availDays
     */
    public function getDays($count, $value) {
        $availDay = array ();
        /**
         * Iterating For loop
         */
        for($j = 0; $j < $count; $j ++) {
            /**
             * set the Values to AvailDay array
             */
            $availDay [] = $value [$j] [1];
        }
        return explode ( ",", implode ( ",", $availDay ) );
    }

    /**
     * Function Name: getAvailDates
     * Get Data
     *
     * @param number $serviceFee
     * @param number $subtotalValue
     * @param int $pDay
     * @param number $overallTotalHours
     * @param number $totalOverNightFee
     */
    public function getAvailDates($getAvailableData) {
        /**
         * set the empty vlaue to null
         */
        $actionMessage = '';
        /**
         * config Value for 'airhotels/custom_group'
         */
        $serviceFee = $this->objectManager->get('\Apptha\Airhotels\Helper\Data')->getServiceFee();
        /**
         * Calculate service fee and $varServiceFee
         *
         * @var unknown
         */
        $serviceFee = round ( ($getAvailableData ['subtotal'] / 100) * ($serviceFee), 2 );
        $varServiceFee = $this->objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency($serviceFee,false,false);
        $convertedServiceFee = $this->objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency($serviceFee,true,false);
        /**
         * Set session for subtotal and service fee
         */
        $this->checkoutSession->setAnyBaseSubtotal ( $getAvailableData ['subtotal'] );
        $this->checkoutSession->setAnyBaseServiceFee ( $serviceFee );
        /**
         * service Fee base Vlaue
         */
        $convertedSubtotal = $this->objectManager->get('Apptha\Airhotels\Block\Booking\View')->convertPrice($getAvailableData ['subtotal']);
        $subtotal = $this->objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency($getAvailableData ['subtotal'],false,false);
        $noDays = $getAvailableData ['pday'];
        /**
         * Action Message.
         * check subcycle is undefined or not
         */
            /**
             * Set subtotal message.
             */
            $actionMessage = $actionMessage . "<p class='subtotal'>" . __ ( 'Subtotal' ) . " </p>
                    <h2 class='bigTotal'>" .( $convertedSubtotal ) . "</h2> <input type='hidden' id='qty' name='qty' value = '$noDays'><input type='hidden' id='subtotal_days' name='subtotal_days' value = '$noDays'> <input type='hidden' id='subtotal_amt' name='subtotal_amt' value = '$subtotal'>";


        /**
         * action Messgae vlaue.
         */
        $actionMessage = $actionMessage . '<p class="subtotal processing">(* ' . __ ( 'Excluding processing fee' ) . " " . $convertedServiceFee . ")
                        <input type='hidden' id='serviceFee' name='serviceFee' value='" .$varServiceFee. "' />

                        <input type='hidden' id='hourly_night_fee' name='hourly_night_fee' value='" . $getAvailableData ['totalovernightfee'] . "' />
                        </p>

                    <div class='clear'></div>
                    ";
        /**
         * Set session for subtotal and service fee
         */

        $this->checkoutSession->setSubtotal ( $subtotal );
        $this->checkoutSession->setServiceFee ( $varServiceFee );
        /**
         * Send the response to body.
         */
        echo $actionMessage;

    }
}
