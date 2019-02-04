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
namespace Apptha\Airhotels\Block\Adminhtml\Renderersource;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Store\Model\StoreManagerInterface;

class Cityimage extends AbstractRenderer {
    
    /**
     * 
     * @var media storage
     */
    protected $_storeManager;
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(StoreManagerInterface $storemanager){
        $this->_storeManager = $storemanager;
    }
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {  
        $imageUrlData = $this->_getValue ( $row );  
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $imageUrl = $mediaDirectory.'Airhotels/Cityimage'.$imageUrlData; 
        return '<img src="'.$imageUrl.'" width="120" height="80" />';
    }
}