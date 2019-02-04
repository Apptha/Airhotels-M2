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
namespace Apptha\Airhotels\Block\Listings;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\StockRegistry;
use Zend\Form\Annotation\Object;

/**
 * This class used to display the products collection
 */
class Places extends \Magento\Framework\View\Element\Template {
    /**
     * Initilize variable for product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    protected $_currency;
    /**
     * Initilize variable for stock registry
     *
     * @var Magento\CatalogInventory\Model\StockRegistry
     */
    protected $stockRegistry;
    protected $messageManager;
    /**
     *
     * @param Template\Context $context
     * @param ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(Template\Context $context, Collection $productFactory, \Magento\Framework\App\Request\Http $request, \Magento\Customer\Model\Session $customerSession, \Magento\Directory\Model\Currency $currency, StockRegistry $stockRegistry, \Magento\Catalog\Model\Product $product, \Magento\Framework\Message\ManagerInterface $messageManager, array $data = []) {
        $this->productFactory = $productFactory;
        $this->product = $product;
        $this->customerSession = $customerSession;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->stockRegistry = $stockRegistry;
        $this->messageManager = $messageManager;
        $this->_currency = $currency;
        $this->request = $request;
        parent::__construct ( $context, $data );
    }
    /**
     * Set product collection uisng ProductFactory object
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $collection = $this->getFilterProducts ();
        $this->setCollection ( $collection );
    }
    /**
     * Getting website current date
     */
    public function currentDate(){
    $objDate = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
    return $objDate->gmtDate();
    }
    /**
     * Getting website base url
     */
    public function getBaseUrl()
    {
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        return $storeManager->getStore()->getBaseUrl();
    }
    /**
     * Getting website media url
     */
    public function getMediaUrl()
    {
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        return $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Prepare layout for products based on places
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        parent::_prepareLayout ();
        return $this;
    }
     /**
     * Getting the products based on city name
     */
    public function getCityProducts($city) {
        /**
         * return array
         */
         return $this->product->getCollection ()->addAttributeToFilter ( 'type_id', array ('eq' => 'booking') )
         ->addAttributeToFilter ( array (array ('attribute' => 'city','like' => "%$city%")) )
         ->addAttributeToSelect ( 'image' )->addAttributeToSelect ( 'user_id' )->addAttributeToSelect ( 'bedtype' )
        ->addAttributeToSelect ( 'name' )->addAttributeToSelect ( 'price' )->addAttributeToSelect ( 'description' )
        ->addAttributeToSelect ( 'short_description' )->addAttributeToSelect ( 'booking_type' )
        ->addAttributeToSelect ( 'rooms' )->addAttributeToSelect ( 'propertyaddress' )->addAttributeToSelect ( 'privacy' )
        ->addAttributeToSelect ( 'status' )->addAttributeToSelect ( 'city' )->addAttributeToSelect ( 'state' )
        ->addAttributeToSelect ( 'country' )->addAttributeToSelect ( 'cancelpolicy' )->addAttributeToSelect ( 'maplocation' )
        ->addAttributeToSelect ( 'accomodates' )->addAttributeToSelect ( 'property_approved' );

    }
    /**
     * Getting the image url status
     *
     * @param string $preImageUrl
     * @param string $currentImageUrl
     * @return number
     */
    public function imageUrlStatus($preImageUrl, $currentImageUrl) {
        /**
         * Getting the imageUrl
         */
        if ($preImageUrl == $currentImageUrl && $preImageUrl != '') {
            $imageUrlStatusForProperty = 1;
        } else {
            $imageUrlStatusForProperty = 0;
        }
        /**
         * Returns the image Url
         */
        return $imageUrlStatusForProperty;
    }
    /**
     * Get current currency symbol
     */
    public function getCurrentCurrencySymbol(){
        return $this->_objectManager->get('\Magento\Directory\Model\Currency')->getCurrencySymbol();
    }
    /**
     *  Getting Wishlist based on customer
     */
    public function customerWishlist($customer){
    return $this->_objectManager->get('\Magento\Wishlist\Model\Wishlist')->loadByCustomerId ( $customer->getId() );
    }
    
    public function toCurrentCurrency($price){
        $toCurrency = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getCurrentCurrency();
        return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseCurrency()->convert($price, $toCurrency);
    }

    /**
     * Funnction Name: getPropertyName
     * reduce the Property name length
     *
     * @param string $propertyName
     *
     * @return multitype:string boolean
     */
    public function getPropertyName($propertyName) {
        $propertyNameSub = substr ( $propertyName, 0, 45 );
        if (strlen ( $propertyName ) > 45) {
            $propertyNameSub .= '...';
        }
        return $propertyNameSub;
    }
}