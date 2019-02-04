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
namespace Apptha\Airhotels\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    const XML_PATH_TITLE = 'airhotels/general/title';
    const XML_PATH_LICENSE_KEY = 'airhotels/general/license_key';
    const XML_SERVICE_FEE = 'airhotels/general/processing_fee';
    const XML_PATH_CANCEL_REQUEST = 'airhotels/general/cancel_request';
    const XML_PAYMENT_REQUEST = 'airhotels/product/payment_request';
    const XML_COMMISSION_FEE = 'airhotels/general/commission_fee';
    const XML_DEFAULT_LOCATION = 'airhotels/advancesearch_configuration/default_location_address';
    const XML_PRICE_SLIDER = 'airhotels/advancesearch_price_slider';
    const XML_PATH_ENABLE_FRONTEND = 'airhotels/general/enable_in_frontend';
    const XML_ADMIN_EMAILS = 'trans_email/ident_general/email';
    const XML_ADMIN_NAME = 'trans_email/ident_general/name';
    
    protected $customerSession;
    protected $customerRepository;
    protected $storeManager;
    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    
    {
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->_collectionFactory = $collectionFactory;
        $this->customerRepository = $customerRepository;
        $this->scopeConfig = $scopeConfig;
        $this->_session = $session;
        parent::__construct ( $context );
    }
    /**
     * Getting store title
     */
    public function getHeadTitle() {
        return $this->scopeConfig->getValue ( static::XML_PATH_TITLE, ScopeInterface::SCOPE_STORE );
    }
    
    /**
     * Function to get processing fee
     */
    public function getProcessingFee() {
        return $this->scopeConfig->getValue ( static::XML_PATH_PROCESSING_FEE, ScopeInterface::SCOPE_STORE );
    }
    
    /**
     * Function to get cancel request
     */
    public function getCancelRequest() {
        return $this->scopeConfig->getValue ( static::XML_PATH_CANCEL_REQUEST, ScopeInterface::SCOPE_STORE );
    }
    /**
     * EnableFrontend
     */
    public function getEnableFrontend() {
        return $this->scopeConfig->getValue ( static::XML_PATH_ENABLE_FRONTEND, ScopeInterface::SCOPE_STORE );
    }
    
    /**
     * Function to get license key
     */
    public function getLicenseKey() {
        return $this->scopeConfig->getValue ( static::XML_PATH_LICENSE_KEY, $storeScope );
    }
    /**
     * Getting store categories list
     * Passed category information as array
     *
     * @param array $categories
     * @return array
     */
    public function showCategoriesTree($categoryName, $catIds) {
        $array = '<ul class="category_ul">';
        foreach ( $categoryName as $key => $catname ) {
            $catChecked = $this->checkSelectedCategory ( str_replace ( 'sub', '', $key ), $catIds );
            if (strstr ( $key, 'sub' )) {
                $key = str_replace ( 'sub', '', $key );
                $array .= '<li class="level-top  parent" id="' . $key . '"><a href="javascript:void(0);"><span class="end-plus" id="' . $key . '_span"></span></a><span class="last-collapse"><div class="ckbox"><input id="cat' . $key . '" type="checkbox" name="category_ids[]" ' . $catChecked . ' value="' . $key . '"><label for="cat' . $key . '">' . $catname . '</label></div></span>';
            } else {
                $array .= '<li class="level-top  parent"><a href="javascript:void(0);"><span class="empty_space"></span></a><div class="ckbox"><input id="cat' . $key . '" type="checkbox" name="category_ids[]"  ' . $catChecked . ' value="' . $key . '"><label for="cat' . $key . '">' . $catname . '</label></div>';
            }
        }
        $array .= '</li>';
        return $array . '</ul>';
    }
    
    /**
     * Function to get the selected category
     *
     * @param array $key
     * @param array $categoryId
     * @return array
     */
    public function checkSelectedCategory($key, $categoryid) {
        $catChecked = '';
        if (in_array ( $key, $categoryid )) {
            $catChecked = 'checked';
        }
        return $catChecked;
    }
    /**
     * Function to get service fee
     *
     * @return boolean
     */
    public function getServiceFee() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue ( static::XML_SERVICE_FEE, $storeScope );
    }
    
    /**
     * Function to get payment request
     *
     * @return boolean
     */
    public function getPaymentRequest() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue ( static::XML_PAYMENT_REQUEST, $storeScope );
    }
    /**
     * Function to get commission fee
     *
     * @return boolean
     */
    public function getCommissionFee() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue ( static::XML_COMMISSION_FEE, $storeScope );
    }
    
    /**
     * Function to get default location
     *
     * @return string
     */
    public function getDefaultLocation() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue ( static::XML_DEFAULT_LOCATION, $storeScope );
    }
    
    /**
     * Function to get default location
     *
     * @return string
     */
    public function getPriceSlider() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue ( static::XML_PRICE_SLIDER, $storeScope );
    }
    /**
     * function contains customer session data
     *
     * @return array
     *
     */
    public function getCustomerData() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Customer\Model\Session' );
    }
    /**
     * Function to get product media url
     *
     * @return url
     */
    public function getProductMediaUrl() {
        $objectManager = $this->getStore ();
        return $objectManager->getBaseUrl( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
    }
    
    /**
     * Get cusomer details
     *
     * @param int $customerId
     *             CustomerId
     *
     *             Add a comment to this line
     * @return object
     */
    public function getCustomerDetails($customerId) {
        return $this->customerRepository->getById ( $customerId );
    }
    /**
     * Function to get admin general email
     *
     * @return string
     */
    public function getAdminEmail() {
        return $this->scopeConfig->getValue ( static::XML_ADMIN_EMAILS, ScopeInterface::SCOPE_STORE );
    }
    
    /**
     * Function to get admin general name
     *
     * @return string
     */
    public function getAdminName() {
        return $this->scopeConfig->getValue ( static::XML_ADMIN_NAME, ScopeInterface::SCOPE_STORE );
    }  
     public function  getStore(){
         return \Magento\Framework\App\ObjectManager::getInstance ()->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ();
     }
     /**
      * Function to get product media url
      *
      * @return url
      */
     public function getListingUrl() {
         $objectManager = $this->getStore ();
         return $objectManager->getUrl('booking/listing/history/');
     }  
    
}