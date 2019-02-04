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
namespace Apptha\Airhotels\Block\Adminhtml\Payments\Grid\Renderer;

/**
 * This class used to renderer payment method in payments grid
 */
class Pay extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        /**
         * Getting customer id by row
         */
        $customerId = $this->_getValue ( $row );
        /**
         * Creating object instance
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get host info by host id
         */
        $hostInfo = $objectManager->get ( 'Apptha\Airhotels\Model\Customerprofile' )->load ( $customerId, 'customer_id' );
        /**
         * Get received amount
         */
        $receivedAmount = $hostInfo->getReceivedAmount ();
        /**
         * Get remaining amount
         */
        $remainingAmount = $hostInfo->getRemainingAmount ();
        /**
         * Get edit url
         */
        $url = $this->getUrl ( '*/*/edit/id/' . $hostInfo->getId () );
        $html = '';
        /**
         * Prepare html content
         */
        if ($receivedAmount == 0 && $remainingAmount == 0) {
            $html = $html . __ ( 'NA' );
        } elseif ($remainingAmount > 0) {
            $html = $html . '<a href="' . $url . '">' . __ ( 'Pay' ) . '</a>';
        } else {
            $html = $html . __ ( 'Paid' );
        }
        /**
         * Return html content
         */
        return $html;
    }
}