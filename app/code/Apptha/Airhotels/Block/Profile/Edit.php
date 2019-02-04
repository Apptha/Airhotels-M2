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
class Edit extends \Magento\Framework\View\Element\Template{
    /**
     * Country list
     * @var object
     */
    protected $_countryFactory;
    /**
     * Get current customer details
     * @var object
     */
    protected $customerSession;
    /**
     * Customer profile model
     * @var object
     */
    protected $_customerProfile;
    /**
     * Attribute colection
     * @var object
     */
    protected $_configOption;
    /**
     *  __construct to load the model collection
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Apptha\Airhotels\Model\Customerprofile $custoemrProfile
     * @param \Magento\Eav\Model\Config $configoption
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param Session $customerSession
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \Apptha\Airhotels\Model\Customerprofile $custoemrProfile,
            \Magento\Eav\Model\Config $configoption,
            \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
            Session $customerSession) {
        $this->customerSession = $customerSession;
        $this->_customerProfile = $custoemrProfile;
        $this->_countryFactory = $countryCollectionFactory;
        $this->_configOption = $configoption;
        parent::__construct ( $context);
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
    public function getCountryList(){
        return $this->getCountryCollection()->toOptionArray(false);
    }
    /**
     * To get the gender list attribute collection
     * @return object
     */
    public function getGenderList(){
        return $this->_configOption->getAttribute('customer', 'gender')->getSource()->getAllOptions(false);
    }
    /**
     * To get the current customer collection
     * @return object
     */
    public function getCustomerDetails(){
        return $this->customerSession->getCustomer();
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
    
    
    
}