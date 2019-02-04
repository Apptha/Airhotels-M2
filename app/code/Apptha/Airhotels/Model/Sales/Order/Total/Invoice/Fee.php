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
namespace Apptha\Airhotels\Model\Order\Sales\Total\Invoice;
class Fee extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal {
    /**
     * Collect invoice grandtotal
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Apptha_Airhotels_Model_Sales_Order_Total_Invoice_Fee
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice) {
        $order = $invoice->getOrder ();
        $feeAmountLeft = $order->getFeeAmount () - $order->getFeeAmountInvoiced ();
        $baseFeeAmountLeft = $order->getBaseFeeAmount () - $order->getBaseFeeAmountInvoiced ();
        /**
         * check the basefeeAmountLeft is greater than BaseGrandTotal
         */
        if (abs ( $baseFeeAmountLeft ) < $invoice->getBaseGrandTotal ()) {
            /**
             * Set invoice grand total and base grand total.
             */
            $invoice->setGrandTotal ( $invoice->getGrandTotal () + $feeAmountLeft );
            $invoice->setBaseGrandTotal ( $invoice->getBaseGrandTotal () + $baseFeeAmountLeft );
        } else {
            /**
             * Fee amount left.
             */
            $feeAmountLeft = $invoice->getGrandTotal () * - 1;
            $baseFeeAmountLeft = $invoice->getBaseGrandTotal () * - 1;
            /**
             * Adding Values the invoice object
             *
             * @value Grand total
             * @value Base Grand total
             */
            $invoice->setGrandTotal ( 0 );
            $invoice->setBaseGrandTotal ( 0 );
        }
        /**
         * Add values of invoice object
         *
         * @values fee amount.
         * @values base fee amount.
         */
        $invoice->setFeeAmount ( $feeAmountLeft );
        $invoice->setBaseFeeAmount ( $baseFeeAmountLeft );
        return $this;
    }
}