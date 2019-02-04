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
namespace Apptha\Airhotels\Block\Adminhtml\Orders\Grid\Renderer;
/**
 * This class contains status functions for listings grid
 * @author user
 *
 */
class Paymentaction extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders approved status
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $getId = $this->_getValue ( $row );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $orderDetails = $objectManager->get ( 'Apptha\Airhotels\Model\Hostorder' )->load ( $getId,'id' );
        
        $transactionArray = array(0 => __ ('Not paid To Hoster'),2 => __ ('Refund To Guest'),3 => __ ('Paid To Hoster'),4 => __ ('Comission Paid'));
        if(array_key_exists($orderDetails->getPaymentStatus(),$transactionArray)){
             $title = $transactionArray[$orderDetails->getPaymentStatus()];
        }else{
             $title = __ ('Pay');
        }
        
        return "<a name=credit href='" . $this->getUrl ( 'airhotelsadmin/payout/edit', array ('id' => $getId) ) . "' title='" . $title . "'>" . $title . "</a>";
    }
}