<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Apptha\Airhotels\Model\Quote\Address\Total;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Item as AddressItem;
use Magento\Quote\Model\Quote\Item;
class Subtotal extends \Magento\Quote\Model\Quote\Address\Total\Subtotal
{
    /**
     * Sales data
     *
     * @var \Magento\Quote\Model\QuoteValidator
     */
    protected $quoteValidator = null;

    /**
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     */
    public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Directory\Model\CurrencyFactory $CurrencyFactory)
    {
        $this->quoteValidator = $quoteValidator;
        $this->checkoutSession = $checkoutSession;
        $this->_currencyFactory = $CurrencyFactory;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Collect address subtotal
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param Address\Total $total
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total

    ) {

        parent::collect($quote, $shippingAssignment, $total);
        $total->setTotalQty ( 0 );
        $address = $shippingAssignment->getShipping()->getAddress();


  /**
   * Process address items
   */
  $items = $this->_getAddressItems ( $address );
  foreach ( $items as $item ) {
   if ($this->_initItem ( $address, $item ) && $item->getQty () > 0) {
    /**
     * Separatly calculate subtotal only for virtual products
     */
    if ($item->getProduct ()->isVirtual ()) {
     /**
      * setting the baseCurrencyCode,currentCurrencyCode
      */



     $baseCurrencyCode = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseCurrencyCode();
     $currentCurrencyCode = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getCurrentCurrencyCode();
     $this->getSubTotal ( $baseCurrencyCode, $currentCurrencyCode, $item );
     
    }
   } else {
    $this->_removeItem ( $address, $item );
   }
    }

    /**
     * Set the subtotal
     */
    $address->setSubtotal ( 120);
    /**
     * Set the base sub total
     */
    $address->setBaseSubtotal ( 120  )->save();
    /**
     * Initialize grand totals
     */
    $this->quoteValidator->validateQuoteAmount ( $address->getQuote (), 120);
    $this->quoteValidator->validateQuoteAmount( $address->getQuote (), 120);
    return $this;
    }

    /**
     * Get the Sub total Value
     *
     * @param string $baseCurrencyCode
     * @param string $currentCurrencyCode
     * @param object $item
     */
    public function getSubTotal($baseCurrencyCode, $currentCurrencyCode, $item) {
        /**
         * check the baseCurrencyCode and currentCurrencyCode are not same
         */
        if ($baseCurrencyCode !== $currentCurrencyCode) {
            /**
             * getting the base currency value
             */
            $rateToBase = $this->_currencyFactory->create()->load($currentCurrencyCode)->getAnyRate($baseCurrencyCode);

            $baseCurrencyPrice = $this->checkoutSession->getAnyBaseSubtotal () * $rateToBase;
            $currentCurrencyPrice = $this->checkoutSession->getAnyBaseSubtotal ();
            $item->setRowTotal ( $currentCurrencyPrice );
            /**
             * Set the base currency price value to Baserow total
             */
            $item->setBaseRowTotal ( $baseCurrencyPrice );
        } else {
            /**
             * Set the basetotal price value to RowTotal.
             */
            $item->setRowTotal ( $this->checkoutSession->getAnyBaseSubtotal () );
            /**
             * Set the base currency price value to Baserow total
             */
            $item->setBaseRowTotal ( $this->checkoutSession->getAnyBaseSubtotal () );
        }
    }


}
