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
 * @version     1.0.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2017 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
/**
 * Clas contains order invoice
 * *
 */
namespace Apptha\Airhotels\Controller\Mytrip;

class Invoice extends \Magento\Framework\App\Action\Action
{

    /**
     *
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     *
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

    /**
     *
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;

    /**
     *
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Sales\Api\OrderRepositoryInterface $orderRepository, \Magento\Sales\Model\Service\InvoiceService $invoiceService, \Magento\Framework\DB\Transaction $transaction, \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender)
    {
        $this->_orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->_invoiceSender = $invoiceSender;
        parent::__construct($context);
    }

    /**
     * Create order invoice .
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->_orderRepository->get($orderId);
        if ($order->canInvoice()) {
            $invoice = $this->_invoiceService->prepareInvoice($order);
            $invoice->register();
            $invoice->save();
            $transactionSave = $this->_transaction->addObject($invoice)->addObject($invoice->getOrder());
            $transactionSave->save();
            $this->_invoiceSender->send($invoice);
            // send notification code
            $order->addStatusHistoryComment(__('Notified customer about invoice #%1.', $invoice->getId()))
                ->setIsCustomerNotified(true)
                ->save();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $hostOrderModel = $objectManager->get('Apptha\Airhotels\Model\Hostorder')->load($orderId, 'order_item_id');
            $hostOrderModel->setOrderStatus('complete');
            $hostOrderModel->save();
            $this->messageManager->addSuccess(__('The invoice has been created successfully.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('sales/order/view/order_id/' . $orderId);
            return $resultRedirect;
        }
    }
}
