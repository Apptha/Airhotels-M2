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
namespace Apptha\Airhotels\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * This class contains country list functions
 */
class Countrylist implements ArrayInterface {
    /**
     * Country list
     * @var object
     */
    protected $_countryFactory;
    /**
     *  __construct to load the model collection
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     */
    public function __construct(\Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory){
        $this->_countryFactory = $countryCollectionFactory;
    }
    /**
     * To create the country collection
     * @return object
     */
    public function getCountryCollection(){
        return $this->_countryFactory->create()->loadByStore();
    }
    /**
     * To convert the country object to array
     * @return array
     */
    public function toOptionArray(){
        return $this->getCountryCollection()->toOptionArray();
    }
    
}