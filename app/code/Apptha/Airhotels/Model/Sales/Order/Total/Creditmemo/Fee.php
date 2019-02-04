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

namespace Apptha\Airhotels\Model\Order\Sales\Total\Creditmemo;
class Fee extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal {
 /**
  * Collect creditmemo grandtotal
  *
  * @param Mage_Sales_Model_Quote_Address $address
  * @return Apptha_Airhotels_Model_Sales_Order_Total_Creditmemo_Fee
  */
 public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo) {
  $order = $creditmemo->getOrder ();
  $feeAmountLeft = $order->getFeeAmountInvoiced () - $order->getFeeAmountRefunded ();
  $basefeeAmountLeft = $order->getBaseFeeAmountInvoiced () - $order->getBaseFeeAmountRefunded ();
  /**
   * check the basefeeAmountLeft is greater than Zero
   */
  if ($basefeeAmountLeft > 0) {
   /**
    * Adding Values the creditmemo object
    */
   $creditmemo->setGrandTotal ( $creditmemo->getGrandTotal () + $feeAmountLeft );
   $creditmemo->setBaseGrandTotal ( $creditmemo->getBaseGrandTotal () + $basefeeAmountLeft );
   $creditmemo->setFeeAmount ( $feeAmountLeft );
   $creditmemo->setBaseFeeAmount ( $basefeeAmountLeft );
  }
  return $this;
 }
}