<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Apptha\Airhotels\Controller\Order;

class ViewAuthorization extends \Magento\Sales\Controller\AbstractController\OrderViewAuthorization
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $orderConfig;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     */
    public function __construct(

        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig
    ) {
        $this->customerSession = $customerSession;
        $this->orderConfig = $orderConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function canView(\Magento\Sales\Model\Order $order)
    {
        $customerId = $this->customerSession->getCustomerId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $hostOrderModel = $objectManager->get('Apptha\Airhotels\Model\Hostorder')->load($order->getId(), 'order_item_id');
        $hostId =$hostOrderModel->getHostId();
        $availableStatuses = $this->orderConfig->getVisibleOnFrontStatuses();
        $checkCustomer =($order->getCustomerId() == $customerId)||($customerId==$hostId);
        if ($order->getId()
            && $order->getCustomerId()
            && $checkCustomer
            && in_array($order->getStatus(), $availableStatuses, true)
        ) {
            return true;
        }
        return false;
    }
}
