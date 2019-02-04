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
namespace Apptha\Airhotels\Block\Profile;
use Magento\Customer\Model\Session;
class Profiledashboardleft extends \Magento\Framework\View\Element\Template{
    /**
     * Get customer profile
     * @var object
     */
    protected $_customerProfile;
    /**
     * Get customer session
     * @var object
     */
    protected $customerSession;
    /**
     *  __construct to load the model collection
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Apptha\Airhotels\Model\Customerprofile $custoemrProfile
     * @param Session $customerSession
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \Apptha\Airhotels\Model\Customerprofile $custoemrProfile,Session $customerSession) {
                $this->customerSession = $customerSession;
                $this->_customerProfile = $custoemrProfile;
                parent::__construct ( $context);
    }
    /**
     * To get the current customer profile data
     * @return object
     */
    public function getCustomerProfileData(){
    $customerData=$this->getCustomerDetails();
    return $this->_customerProfile->load ( $customerData->getId(), 'customer_id' );
    }
    
    /**
     * To return the customer profile image
     * @return string
     */
    public function getCustomerProfileImage(){
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectModelManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'Airhotels/Customerprofileimage/Resized';
    }
    /**
     * To get the current customer collection
     * @return object
     */
    public function getCustomerDetails(){
    return $this->customerSession->getCustomer();
    }
    /**
     * To get address book url
     * @return string
     */
    public function getAddressbookUrl(){
        return $this->getUrl('customer/address');
    }
    
    /**
     * news letter url
     * @return string
     */
    public function getNewsletterUrl(){
        return $this->getUrl('newsletter/manage');
    }
    
    /**
     * To get the edit profile data url
     * @return string
     */
    public function getEditProfileUrl(){
        return $this->getUrl('booking/profile/edit');
    }
    /**
     * To get the order data url
     * @return string
     */
     public function getOrderHistoryUrl(){
        return $this->getUrl('sales/order/history');
    }
      /**
     * Customer trips URL
     * @return string
     */
    public function getTripUrl(){
        return $this->getUrl('airhotels/mytrip/upcomingtrip');
    }
}