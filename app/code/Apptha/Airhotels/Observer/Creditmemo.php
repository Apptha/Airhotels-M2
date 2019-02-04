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
use Magento\Sales\Model\Order\Email\Container\CreditmemoIdentity;
use Magento\Sales\Model\Order\Address\Renderer;

/**
 * This class contains creditmemo save after 
 */
class Creditmemo implements ObserverInterface
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

    public function __construct(CreditmemoIdentity $identityContainer, 
    Renderer $addressRenderer, PaymentHelper $paymentHelper)
    
    {
        $this->paymentHelper = $paymentHelper;
        $this->identityContainer = $identityContainer;
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * Execute the result
     *
     * @see \Magento\Framework\Event\ObserverInterface::execute()
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        /**
         * Get order from creditmemo
         */
        $order = $creditmemo->getOrder();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * Load hostorder data by order id
         */
        $hostOrder = $objectManager->get('Apptha\Airhotels\Model\Hostorder')->load($creditmemo->getOrderId(), 'order_item_id');
        /**
         * Changing the order status has "Closed" in airhotels_hostorder' table when creditmemo generated."
         */
        $hostOrder->setOrderStatus('closed')->save();
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
        $adminName = $admin->getAdminName();
        $adminEmail = $admin->getAdminEmail();
        $templateId = 'airhotels_host_order_creditmemo';
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
        $product = $objectManager->get('Magento\Catalog\Model\Product')->load($hostOrder->getEntityId());
        $commissionPercent = $objectManager->get ( 'Apptha\Airhotels\Helper\Data' )->getCommissionFee();
        $totalAmount =  $order->getGrandTotal ();
        $adminFee =  ($order->getSubtotal () * $commissionPercent) / 100;
        $refundAmount = $order->getSubtotal ();
        $currencyHelper=$objectManager->get ('Apptha\Airhotels\Helper\Order' )->getCurrencySymbol();
        /* Template variables Detail */
        $emailTempVariables = (array(
            'cname' => $hostName,
            'creditmemo' => $creditmemo,
            'order' => $order,
            'comment' => $creditmemo->getCustomerNoteNotify() ? $creditmemo->getCustomerNote() : '',
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $order->getStore(),
            'product_name' => $product->getName (),
            'total'=>$currencyHelper.$totalAmount,
            'admin_fee'=>$currencyHelper.$adminFee,
            'refund_amount'=>$currencyHelper.$refundAmount,
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order)
        ));
        /*
         * We write send mail function in helper because if we want to
         * use same in other action then we can call it directly from helper
         */
        
        /* call send mail method from helper or where you define it */
        $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId);
        $this->sendOrderStatusEmail($order );
    }
     /**
     * Sending order status email to customer
     * 
     * @param Order $order
     * @return void
     */
    public function sendOrderStatusEmail($order ){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * Load hostorder data by order id
         */
        $hostOrder = $objectManager->get('Apptha\Airhotels\Model\Hostorder')->load($order->getId(), 'order_item_id');
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
        $customerEmail = $customer->getEmail();
        $customerName = $customer->getName();
        $templateId = 'airhotels_order_status';
        /* Template variables Detail */
        $emailTempVariables = (array(
            'customername' => $customerName,
            'orderstatus' => $order->getStatus(),
            'incrementid' => $order->getIncrementId(),
        ));
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
        /*
         * We write send mail function in helper because if we want to
         * use same in other action then we can call it directly from helper
         */
        
        /* call send mail method from helper or where you define it */
        $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId, $recipient);

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