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
 * @version     0.2.9
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
namespace Apptha\Airhotels\Model\Order\Sales\Quote\Address\Total;
class Fee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {

 /**
  * Collect fee grandtotal
  *
  * @param Mage_Sales_Model_Quote_Address $address
  * @return Apptha_Airhotels_Model_Sales_Quote_Address_Total_Fee
  */
 public function collect(\Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total) {
  /**
   * Call the PArent Value.
   */
  parent::collect ( $address );
  /**
   * SetAmount Value.
   */
  $this->_setAmount ( 0 );
  /**
   * Set baseAmount Vlaue.
   */
  $this->_setBaseAmount ( 0 );

  $items = $this->_getAddressItems ( $address );
  if (! count ( $items )) {
   return $this;
  }

  /**
   * Quote Vlaue.
   */
  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
  $quote = $address->getQuote ();
  /**
   * Exist Amout Value.
   */
  $existAmount = $quote->getFeeAmount ();
  /**
   * Get the service fee amount
   */
  $processingFee = $objectManager->get('\Magento\Checkout\Model\Session')->getServiceFee();
  $objectManager->get('\Magento\Checkout\Model\Session')->setAnySessionFinalFeeAmount ( $processingFee );
  /**
   * Get base currency code.
   * Get currnet currency code.
   */
  $baseCurrencyCode = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseCurrencyCode();
  $currentCurrencyCode = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getCurrentCurrencyCode();
  /**
   * Check the 'base curreency Code' and 'currentCurrencyCode' are not same.
   */
 if ($baseCurrencyCode !== $currentCurrencyCode) {
   /**
    * Get the Fee Value.
    */
     $rateToBase = $objectManager->get('\Magento\Directory\Model\CurrencyFactory')->create()->load($currentCurrencyCode)->getAnyRate($baseCurrencyCode);

   $fee = $processingFee * $rateToBase;
   $existAmountIni = $existAmount * $rateToBase;
   /**
    * Set the Balance Vlaue.
    */
   $balance = $fee - $existAmountIni;
   /**
    * In cart page processing fees
    */
   $address->setFeeAmount ( $balance );
   $address->setBaseFeeAmount ( $processingFee );
   $quote->setFeeAmount ( $balance );
   /**
    * Add the service fee to the grandtotal
    */
   $address->setGrandTotal ( $address->getGrandTotal () + $address->getFeeAmount () );
   /**
    * Address Value for 'setBaseGrandTotal'
    */
   $address->setBaseGrandTotal ( $address->getBaseGrandTotal () + $address->getBaseFeeAmount ());
  }
  /**
   * Check the value of '$baseCurrencyCode' and $currentCurrencyCode are same.
   */
  if ($baseCurrencyCode === $currentCurrencyCode) {
   /**
    * Fee for process.
    */
   $fee = $processingFee;
   $balance = $fee - $existAmount;
   /**
    * Set the balance fee
    */
   $address->setFeeAmount ( $balance );
   $address->setBaseFeeAmount ( $balance );
   $quote->setFeeAmount ( $balance );
   /**
    * Add the service fee to the grandtotal
    */
   $address->setGrandTotal ( $address->getGrandTotal () + $address->getFeeAmount ());
   $address->setBaseGrandTotal ( $address->getBaseGrandTotal () + $address->getBaseFeeAmount ());
  }
 }
 /**
  * Function Name: fetch
  * Retrive the base fee amount
  *
  * @param Mage_Sales_Model_Quote_Address $address
  * @return Apptha_Airhotels_Model_Sales_Quote_Address_Total_Fee
  */
 public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total) {
  /**
   * To retrive the property service fees from table Sales_Flat_Quote_Address
   *
   * @return int value
   */
  $amt = $address->getFeeAmount ();

   $hourlyExcludedFeeMsg = '';


   /**
    * Set the address Value to
    * 'code',
    * 'title',
    * 'value'
    */
   $address->addTotal ( array (
     'code' => $this->getCode (),
     'title' => $hourlyExcludedFeeMsg . "<span>" . __ ( 'Processing Fee' ) . "</span>",
     'value' => $amt
   ) );

  return $this;
 }

}