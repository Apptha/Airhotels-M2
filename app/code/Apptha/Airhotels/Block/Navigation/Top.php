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
namespace Apptha\Airhotels\Block\Navigation;
class Top extends \Magento\Framework\View\Element\Template{
    
    /**
     * Edit profile edit URL
     * @return string
     */
    public function getEditProfileUrl(){
        return $this->getUrl('airhotels/profile/edit');
    }
    /**
     * get manage listing url
     * @return string
     */
    public function getListingUrl(){
        return $this->getUrl('airhotels/listing/manage');
    }
    /**
     * Customer dashboard URL
     * @return string
     */
    public function getDashboardUrl(){
        return $this->getUrl('customer/account/index');
    }
    /**
     * Customer message URL
     * @return string
     */
    public function getMessageUrl(){
        return $this->getUrl('airhotels/message/inbox');
    }
    /**
     * Customer trips URL
     * @return string
     */
    public function getTripUrl(){
        return $this->getUrl('airhotels/mytrip/currenttrip');
    }
    
    
}