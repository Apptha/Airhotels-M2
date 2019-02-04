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
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\InvoiceIdentity;
use Magento\Sales\Model\Order\Address\Renderer;

/**
 * This class contains order refund functions
 */
class Invoice implements ObserverInterface
{

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

    public function __construct(InvoiceIdentity $identityContainer, Renderer $addressRenderer, PaymentHelper $paymentHelper)
    
    {
        $this->paymentHelper = $paymentHelper;
        $this->identityContainer = $identityContainer;
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * Get Order Details
         *
         * @var unknown
         */
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        $orderId = $order->getEntityId();
        
        foreach ($invoice->getAllItems() as $item) {
            
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }
            
            /**
             * Get Product Data
             *
             * @var int(Product Id)
             */
            $productId = $item->getProductId();
            /**
             * Create object instance
             */
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /**
             * Load product data by product id
             */
            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
            /**
             * Assign host id
             */
            $hostId = $product->getUserId();
            
            /**
             * Checking for host id exist or not
             */
            if (! empty($hostId)) {
                $hostOrderCollection = $objectManager->get('Apptha\Airhotels\Model\Hostorder')->load($orderId, 'order_item_id');
                $hostOrderAmount = $hostOrderCollection->getHostAmount();
                $hostOrderCollection->setOrderStatus('complete')->save();
                $this->updateHostAmount($hostId, $hostOrderAmount);
                $this->sendInvoiceEmailToHost($invoice, $order,$product );
            }
        }
    }

    /**
     * Update host amount
     *
     * @param int $updatehostId
     * @param double $totalAmount
     *
     * @return void
     */
    public function updateHostAmount($hostId, $hostOrderAmount)
    {
        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * Load host by host id
         */
        $hostModel = $objectManager->get('Apptha\Airhotels\Model\Customerprofile');
        $hostDetails = $hostModel->load($hostId, 'customer_id');
        /**
         * Get remaining amount
         */
        $remainingAmount = $hostDetails->getRemainingAmount();
        /**
         * Total remaining amount
         */
        $totalRemainingAmount = $remainingAmount + $hostOrderAmount;
        /**
         * Set total remaining amount
         */
        $hostDetails->setRemainingAmount($totalRemainingAmount);
        /**
         * Save remaining amount
         */
        $hostDetails->save();
    }

    /**
     * Sending invoice email to host
     * 
     * @param Order $hostOrder
     * @param Order $order
     * @param Order $product
     * @return void
     */
    public function sendInvoiceEmailToHost($invoice, $order,$product ){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * Load hostorder data by order id
         */
        $hostOrder = $objectManager->get('Apptha\Airhotels\Model\Hostorder')->load($invoice->getOrderId(), 'order_item_id');
        // loading customer details
        $host = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getHostId());
        /**
         * Property Email Owner
         */
        $recipient = $host->getEmail();
        /**
         * Property Email Owner
         */
        $hostName = $host->getName();
        $admin = $objectManager->get('Apptha\Airhotels\Helper\Data');
        /**
         * Assign admin details
         */
        $adminEmail = $admin->getAdminEmail();
        $adminSales = $objectManager->get('Apptha\Airhotels\Helper\Order');
        $salesName = $adminSales->getStoreSalesName();
        $salesEmail = $adminSales->getStoreSalesEmail();
        $templateId = 'airhotels_host_order_invoice_create_after';
        $commissionPercent = $objectManager->get ( 'Apptha\Airhotels\Helper\Data' )->getCommissionFee();
        $totalAmount =  $objectManager->get('Apptha\Airhotels\Helper\General')->priceConverter($order->getGrandTotal ());
        $_adminFee = ($order->getSubtotal () * $commissionPercent) / 100;
        $adminFee = $objectManager->get('Apptha\Airhotels\Helper\General')->priceConverter($_adminFee);
        $ownerAmount = $objectManager->get('Apptha\Airhotels\Helper\General')->priceConverter($order->getSubtotal ());
        /**
         * mail sender name
         */
        /* Sender Detail */
        $senderInfo = [
            'name' => $salesName,
            'email' => $salesEmail
        ];
        /* Receiver Detail */
        $receiverInfo = [
            'name' => $hostName,
            'email' => $recipient
        ];
        /* Template variables Detail */
        $emailTempVariables = (array(
            'hostname' => $hostName,
            'order' => $order,
            'invoice' => $invoice,
            'comment' => $invoice->getCustomerNoteNotify() ? $invoice->getCustomerNote() : '',
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            'product_name' => $product->getName (),
            'total'=>$totalAmount,
            'admin_fee'=>$adminFee,
            'owner_amount'=>$ownerAmount,
        ));
        /*
         * We write send mail function in helper because if we want to
         * use same in other action then we can call it directly from helper
         */
        
        /* call send mail method from helper or where you define it */
        $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId, $adminEmail);
        //send order status email to customer
        $this->sendOrderStatusEmail($hostOrder);
    }
    /**
     *
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedShippingAddress($order){
        return $order->getIsVirtual() ? null : $this->addressRenderer->format($order->getShippingAddress(), 'html');
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
     *
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedBillingAddress($order){
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }
    /**
     * Sending order status email to customer
     * 
     * @param Order $hostOrder
     * @return void
     */
    public function sendOrderStatusEmail($hostOrder ){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $admin = $objectManager->get('Apptha\Airhotels\Helper\Data');
        /**
         * Assign admin details
         */
        $adminName = $admin->getAdminName();
        $adminEmail = $admin->getAdminEmail();
        // loading customer details
        $host = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getHostId());
        /**
         * Property Email Owner
         */
        $recipient = $host->getEmail();
        $customer = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getCustomerId());
        $customerName = $customer->getName();
        $customerEmail = $customer->getEmail();
        $templateId = 'airhotels_order_status';
        /**
         * mail sender name
         */
        /* Sender Detail */
        $senderInfo = [
            'name' => $adminName,
            'email' => $adminEmail
        ];
        /* Receiver Detail */
        $receiverInfo = [
            'name' => $customerName,
            'email' => $customerEmail
        ];
        /* Template variables Detail */
        $emailTempVariables = (array(
            'customername' => $customerName,
            'orderstatus' => $hostOrder->getOrderStatus(),
            'incrementid' => $hostOrder->getOrderId(),
        ));
        /*
         * We write send mail function in helper because if we want to
         * use same in other action then we can call it directly from helper
         */
        
        /* call send mail method from helper or where you define it */
        $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId, $recipient);
    }
}