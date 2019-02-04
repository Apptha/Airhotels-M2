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
namespace Apptha\Airhotels\Model\Total;


class Fee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
   /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null;

    public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency)
    {
        $this->quoteValidator = $quoteValidator;
        $this->_priceCurrency = $priceCurrency;
    }
  public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSession = $objectManager->get('\Magento\Checkout\Model\Session');
        $fee = $checkoutSession->getServiceFee();
        $grandTotal = $checkoutSession->getAnyBaseSubtotal();
        $storeObj=$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
        $baseCurrencyCode = $storeObj->getBaseCurrencyCode();
        $currentCurrencyCode = $storeObj->getCurrentCurrencyCode();
        if($baseCurrencyCode==$currentCurrencyCode){
            $total->setTotalAmount('fee',  $fee);
            $total->setBaseTotalAmount('fee',  $fee);
            $total->setFee($fee);
            $total->setBaseFee($fee);
            $total->setSubtotal($grandTotal + $fee);
            $total->setBaseSubtotal($grandTotal + $fee);
            $total->setGrandTotal(0);
            $total->setBaseGrandTotal(0);
        }else{
            $rate =($fee)?($this->_priceCurrency->convert($fee, $storeObj->getStoreId()) / $fee):0;
            $calfee = ($rate)?($fee / $rate):0;
            $total->setTotalAmount('fee',  $fee);
            $total->setBaseTotalAmount('fee',  $calfee);
            $total->setFee($fee);
            $total->setBaseFee($calfee);
            $total->setSubtotal($grandTotal + $fee);
            $total->setBaseSubtotal($grandTotal + $calfee);
            $total->setGrandTotal(0);
            $total->setBaseGrandTotal(0);
        }
        return $this;
    }

    protected function clearValues(Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
        $total->setGrandTotal(0);
        $total->setBaseGrandTotal(0);
    }
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array|null
     */
    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSession = $objectManager->get('\Magento\Checkout\Model\Session');
        return [
            'code' => 'fee',
            'title' => 'Fee',
            'value' => $checkoutSession->getServiceFee()
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Fee');
    }
}