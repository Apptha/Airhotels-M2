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
 * Tax totals modification block. Can be used just as subblock of \Magento\Sales\Block\Order\Totals
 */
namespace Apptha\Airhotels\Block\Adminhtml\Sales\Order\Invoice;


class Extrafee extends \Magento\Framework\View\Element\Template {
    
        protected $_config;
        protected $_order;
        protected $_source;

        public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Tax\Model\Config $taxConfig,
            array $data = []
        ) {
            $this->_config = $taxConfig;
            parent::__construct($context, $data);
        }
        
        public function getSource()
        {
            return $this->_source;
        } 
        
        public function getStore()
        {
            return $this->_order->getStore();
        }
        
        public function displayFullSummary()
        {
            return true;
        }
        
        public function getOrder()
        {
            return $this->_order;
        }
        
        public function getValueProperties()
        {
            return $this->getParentBlock()->getValueProperties();
        }
        
        public function getLabelProperties()
        {
            return $this->getParentBlock()->getLabelProperties();
        }
        
         public function initTotals()
        {
            $parent = $this->getParentBlock();
            $this->_order = $parent->getOrder();
            $this->_source = $parent->getSource();
            $orderCurrency=$this->_order->getOrderCurrencyCode();
            $baseCurr=$this->_order->getBaseCurrencyCode();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $priceCurrencyObject = $objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface');
            $store = $this->_order->getStoreId();
            $calfee= $this->_order->getFeeAmount();
            if($orderCurrency!==$baseCurr){
                $calfee = $priceCurrencyObject->convert($this->_order->getFeeAmount(), $store, $orderCurrency);
            }
            $fee = new \Magento\Framework\DataObject(
                        [
                            'code' => 'fee',
                            'strong' => false,
                            'value' =>  $calfee,
                            'base_value' => $this->_order->getFeeAmount(),
                            'label' => __('Fee'),
                        ]
                    );
            $parent->addTotal($fee, 'fee');
            return $this;
        }
}