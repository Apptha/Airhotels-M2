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
 * */
namespace Apptha\Airhotels\Model\Order\Invoice\Total;
use  Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
class Cost extends AbstractTotal
{
    /**
     * Collect total cost of invoiced items
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        
        $orderId = $invoice->getOrderId();
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->getCollection();
        $orderDatamodel = $orderDatamodel->AddFieldToFilter('entity_id',$orderId);
        foreach($orderDatamodel as $orderDatamodel1){
            $grandTotal = $orderDatamodel1->getBaseGrandTotal();
        }
        $baseInvoiceTotalCost = 0;
        foreach ($invoice->getAllItems() as $item) {
            if (!$item->getHasChildren()) {
                $baseInvoiceTotalCost += $item->getBaseCost() * $item->getQty();
            }
        }
        $invoice->setBaseCost($baseInvoiceTotalCost);
        
        $invoice->setGrandTotal($grandTotal);
        return $this;
    }
}