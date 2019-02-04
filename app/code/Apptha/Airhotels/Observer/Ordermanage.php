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
namespace Apptha\Airhotels\Observer;

use Magento\Framework\Event\ObserverInterface;
use Apptha\Airhotels\Helper\Data;
use Apptha\Airhotels\Helper\Dateformat;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Address\Renderer;

/**
 * This class contains saving order details functions
 */
class Ordermanage implements ObserverInterface
{

    protected $airhotelsData;
    protected $helperDateFormat;

    protected $systemHelper;

    /**
     *
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     *
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     *
     * @param Data $airhotelsData
     */
    public function __construct(Data $airhotelsData, DateFormat $helperDateFormat, \Magento\Checkout\Model\Session $checkoutSession, OrderIdentity $identityContainer, Renderer $addressRenderer, PaymentHelper $paymentHelper)
    {
        $this->airhotelsData = $airhotelsData;
        $this->checkoutSession = $checkoutSession;
        $this->paymentHelper = $paymentHelper;
        $this->identityContainer = $identityContainer;
        $this->addressRenderer = $addressRenderer;
        $this->helperDateFormat = $helperDateFormat;
    }

    /**
     * Execute the result
     *
     * @see \Magento\Framework\Event\ObserverInterface::execute()
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * Getting order ids
         */
        $order = $observer->getOrderIds();
        /**
         * Assign first order id from order array
         */
        $orderId = $order[0];
        /**
         * Create instance for object manage
         */
        $serviceFee = $this->checkoutSession->getServiceFee();
        $accomodates = $this->checkoutSession->getAccomodate();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * Get order details by order id
         */
        $orderDetails = $objectManager->get('Magento\Sales\Model\Order');
        $orderData = $orderDetails->load($orderId);
        $orderData->setFeeAmount($serviceFee);
        $orderData->setBaseFeeAmount($serviceFee);
        $orderData->save();
        $grandTotal = $orderData->getGrandTotal();
        $orderTotal = $grandTotal - $serviceFee;
        $orderStatus = $orderData->getStatus();
        /**
         * Get order currency code
         */
        $currencyCode = $orderData->getOrderCurrencyCode();
        $orderId = $orderData->getEntityId();
        /**
         * Get order increment id
         */
        $incrementId = $orderData->getIncrementId();
        /**
         * Get ordered customer id
         */
        $customerId = $orderData->getCustomerId();
        $billingId = $orderData->getBillingAddressId();
        $quoteId = $orderData->getQuoteId();
        /**
         * Get order items
         */
        $orderItems = $orderData->getAllItems();
        /**
         * saving each order items
         */
        foreach ($orderItems as $item) {
            $productId = $item->getProductId();
            $productOptions = $item->getProductOptions();
            $objDate = $objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
            $from = $objDate->date('Y-m-d', strtotime($this->helperDateFormat->searchDateFormat($productOptions['info_buyRequest']['from'])));
            $to = $objDate->date('Y-m-d', strtotime($this->helperDateFormat->searchDateFormat($productOptions['info_buyRequest']['to'])));
            $product = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
            $hostId = $product->getUserId();
            if (! empty($hostId)) {
                $productCommission = $this->airhotelsData->getCommissionFee();
                $commissionFee = ($productCommission / 100) * $orderTotal;
                $hostAmount = $grandTotal - $commissionFee - $serviceFee;
                $productName = $item->getName();
                $hostOrderModel = $objectManager->create('Apptha\Airhotels\Model\Hostorder');
                $hostOrderModel->setHostId($hostId)
                    ->setOrderItemId($orderId)
                    ->setHostAmount($hostAmount)
                    ->setEntityId($productId)
                    ->setHostProductTotal($grandTotal)
                    ->setCommissionFee($commissionFee)
                    ->setBillingId($billingId)
                    ->setQuoteId($quoteId)
                    ->setServiceFee($serviceFee)
                    ->setAccomodates($accomodates)
                    ->setListingName($productName)
                    ->setHostAmount($hostAmount)
                    ->setOrderId($incrementId)
                    ->setOrderCurrencyCode($currencyCode)
                    ->setCustomerId($customerId)
                    ->setOrderStatus($orderStatus)
                    ->setFromdate($from)
                    ->setTodate($to)
                    ->save();
                $this->sendOrderEmailToHost($hostOrderModel, $orderData,$product);
            }
        }
    }
    /**
     * Sending order email to host
     * 
     * @param Order $hostOrder
     * @param Order $order
     * @param Product $product
     * @return void
     */
    public function sendOrderEmailToHost($hostOrder, $order, $product){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $host = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getHostId());
        $recipient = $host->getEmail();
        $admin = $objectManager->get('Apptha\Airhotels\Helper\Data');
        /**
         * Assign admin details
         */
        $adminName = $admin->getAdminName();
        $adminEmail = $admin->getAdminEmail();
        /**
         * Property Email Owner
         */
        $hostName = $host->getName();
        $templateId = 'airhotels_order_notification_template';
        /* Sender Detail */
        $senderInfo = [
            'name' => $adminName,
            'email' => $adminEmail
        ];
        /* Receiver Detail */
        $receiverInfo = [
            'name' => $hostName,
            'email' => $recipient
        ];
         /**
         * get proeprty details
         */
        $propertyName = $product->getName ();
        $commissionPercent = $objectManager->get ( 'Apptha\Airhotels\Helper\Data' )->getCommissionFee();
        $totalAmount =  $objectManager->get('Apptha\Airhotels\Helper\General')->priceConverter($order->getGrandTotal ());
        $_adminFee = ($order->getSubtotal () * $commissionPercent) / 100;
        $adminFee = $objectManager->get('Apptha\Airhotels\Helper\General')->priceConverter($_adminFee);
        $ownerAmount = $objectManager->get('Apptha\Airhotels\Helper\General')->priceConverter($order->getSubtotal ());
        /**
         * mail sender name
         */
        $emailTempVariables = (array (
                'ownername' => $adminName,
                'customer_email' => $order->getCustomerEmail (),
                'customer_firstname' => $order->getCustomerFirstname (),
                'order_id' => $order->getIncrementId (),
                'product_name' => $propertyName,
                'total'=>$totalAmount,
                'admin_fee'=>$adminFee,
                'owner_amount'=>$ownerAmount,
                'billing' => $order->getBillingAddress(),
                'payment_html' => $this->getPaymentHtml($order),
                'store' => $order->getStore(),
                'formattedBillingAddress' => $this->getFormattedBillingAddress($order)
        ));
        /*
         * We write send mail function in helper because if we want to
         * use same in other action then we can call it directly from helper
         */
        
        /* call send mail method from helper or where you define it */
        $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId);
    }
    /**
     * Return payment info block as html
     *
     * @param Order $order
     * @return string
     */
    protected function getPaymentHtml(Order $order){
        return $this->paymentHelper->getInfoBlockHtml($order->getPayment(), $this->identityContainer->getStore()
            ->getStoreId());
    }
    /**
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedBillingAddress($order){
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }

}