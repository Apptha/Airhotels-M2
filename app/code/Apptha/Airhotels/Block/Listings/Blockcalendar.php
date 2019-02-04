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
 * */
namespace Apptha\Airhotels\Block\Listings;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Resource\Product\CollectionFactory;
use Zend\Form\Annotation\Object;
class Blockcalendar extends \Magento\Framework\View\Element\Template
{
     public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Customer\Model\Customer $customer, \Magento\Review\Model\ReviewFactory $reviewFactory, \Magento\Review\Model\ResourceModel\Review\Product\Collection $review, \Apptha\Airhotels\Model\Customerprofile $customerProfile, \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute, \Magento\Eav\Model\Config $eavConfig, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attributeSet, \Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState, CategoryRepositoryInterface $categoryRepository, \Magento\Framework\Registry $registry, \Magento\Customer\Model\Session $customerSession, array $data = []) {
        parent::__construct ( $context, $data );

        $this->attributeSet = $attributeSet;
        $this->product = $product;
        $this->review = $review;
        $this->reviewFactory = $reviewFactory;
        $this->_eavAttribute = $eavAttribute;
        $this->_registry = $registry;
        $this->eavConfig = $eavConfig;
        $this->customer = $customer;
        $this->_customerProfile = $customerProfile;
        $this->customerSession = $customerSession;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->categoryRepository = $categoryRepository;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();

     }

    /**
     * Get current base currency
     */
    public function getCurrentBaseCurrency(){

        $currencysymbol = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface');


        return $currencysymbol->getStore()->getCurrentCurrencyCode();
    }

     /**
     * Get current product from registry
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct() {

        return $this->_registry->registry('current_product');
    }

    /**
     * Get product data
     * @param productId int
     * @return object
     */
    public function getProductData($productId) {
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      return $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
    }

    /**
     * To return the customer profile image
     * @return string
     */

     public function getHostData($customerId){
        return $this->_customerProfile->load ( $customerId, 'customer_id' );
    }
    /**
     * To return the customer profile image
     * @return string
     */
    public function getHostImageUrl(){
        return $this->objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'Airhotels/Customerprofileimage/Resized';
    }
    /**
     * reduce Property name length
     *
     * @param string $propertyName
     *
     * @return multitype:string boolean
     */
    public function getListingName($listingName) {
        $listingNameSub = substr ( $listingName, 0, 45 );
        if (strlen ( $listingName ) > 45) {
            $listingNameSub .= '...';
        }
        return $listingNameSub;
    }

    /**
     * Get converted curreny
     *
     * @return int
     */
        public function convertPrice($amount)
        {
            $currency = null;
            $priceCurrencyObject = $this->objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface');
            $storeManager = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface');
            $store = $storeManager->getStore()->getStoreId();
            $rate = $priceCurrencyObject->convert($amount, $store, $currency);


            $rate = $priceCurrencyObject->convert($amount, $store) / $amount;
            $amount = $amount / $rate;

            return $priceCurrencyObject->round($amount);
        }

    /**
     * Get current currency symbol
     */
    public function getCurrentCurrencySymbol(){

    $storeManager = $this->objectManager->get('Magento\Store\Model\StoreManagerInterfa‌​ce');
    $currencyCode = $storeManager->getStore()->getCurrentCurrencyCode();
    $currency = $this->objectManager->get('Magento\Directory\Model\CurrencyFact‌​ory')->create()->loa‌​d($currencyCode);
    return $currency->getCurrencySymbol();

    }

    /**
     * Get base currency symbol
     */
    public function getBaseCurrencySymbol(){

        $storeManager = $this->objectManager->get('Magento\Store\Model\StoreManagerInterfa‌​ce');
        $currencyCode = $storeManager->getStore()->getBaseCurrencyCode();
        $currency = $this->objectManager->get('Magento\Directory\Model\CurrencyFact‌​ory')->create()->loa‌​d($currencyCode);
        return $currency->getCurrencySymbol();

    }

    /**
     * Getting website base url
     */
    public function getBaseUrl()
    {
        $storeManager = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface');

        return $storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get add to cart url
     * @return URL
     */
    public function getCartUrl($product) {

        return $this->objectManager->get('Magento\Checkout\Helper\Cart')->getAddUrl($product);
    }
    /**
     * Get customer wishlist
     *
     * @return object
     */
    public function getWishlistCollection() {
        return $this->objectManager->get('\Magento\Wishlist\Controller\WishlistProviderInterface')->getWishlist()->getItemCollection();
    }

    /**
     * Get eav attribute collection
     *
     * @return string
     */
    public function getEav() {
        return $this->objectManager->get ( 'Magento\Eav\Model\Entity\Attribute' )->getCollection ();
    }

    /**
     * Get eav attribute group collection
     *
     * @return string
     */
    public function getEavGroup() {
        return $this->objectManager->get ( 'Magento\Eav\Model\Entity\Attribute\Group' )->getCollection ();
    }

    /**
     * retrieve attribute id for product attributes
     *
     * @return integer
     */
    public function getAttributeId($attributeId) {
        /**
         * Getting entity attribute model
         */
        return $this->_eavAttribute->getIdByCode('catalog_product', $attributeId);
    }

    /**
     * Get media image url
     *
     * @return string $mediaImageUrl
     */
    public function getMediaImageUrl() {
        return $this->objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'catalog/product';
    }
    /**
     * Prepare rating stars
     *
     * @param int $count
     *         rating count
     * @return string $htmlElements rating html content
     */
    public function showratingCode($count = 0) {
        $htmlElements = '';
        /**
         * Adding the images
         */
        for($x = 1; $x <= $count; $x ++) {
            //$htmlElements = $htmlElements . "<img style='float:left'  src='" . $this->getSkinUrl ( 'images/red.png' ) . "' width='16' height='16' alt='' />";
        }
        for($i = $x; $i <= 5; $i ++) {
            //$htmlElements = $htmlElements . "<img style='float:left'  src='" . $this->getSkinUrl ( 'images/grey.png' ) . "' width='16' height='16' alt=''/>";
        }
        /**
         * Return html elements.
         */
        return $htmlElements;
    }

    /**
     * Get similar listings
     *
     * @param $city, $productId
     * @return object
     */
    public function getSimilarLocation($city, $productId) {

        return $this->product->getCollection ()->addAttributeToFilter ( 'type_id', array (
                'eq' => 'booking'
        ) )->addAttributeToSelect ( 'image' )->addAttributeToSelect ( 'user_id' )->addAttributeToSelect ( 'bedtype' )->addAttributeToSelect ( 'name' )->addAttributeToSelect ( 'price' )->addAttributeToSelect ( 'description' )->addAttributeToSelect ( 'short_description' )->addAttributeToSelect ( 'booking_type' )->addAttributeToSelect ( 'amenity' )->addAttributeToSelect ( 'rooms' )->addAttributeToSelect ( 'propertyaddress' )->addAttributeToSelect ( 'privacy' )->addAttributeToSelect ( 'status' )->addAttributeToSelect ( 'city' )->addAttributeToSelect ( 'state' )->addAttributeToSelect ( 'country' )->addAttributeToSelect ( 'cancelpolicy' )->addAttributeToSelect ( 'accomodates' )->addAttributeToSelect ( 'property_approved' );
    }

    /**
     * Retrieve url for add product to cart
     * Will return product view page URL if product has required options
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     *
     * @return string
     */
    public function getAddToCartUrl($product, $additional = []) {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        if ($product->getTypeInstance ()->hasRequiredOptions ( $product )) {
            if (! isset ( $additional ['_escape'] )) {
                $additional ['_escape'] = true;
            }
            if (! isset ( $additional ['_query'] )) {
                $additional ['_query'] = [ ];
            }
            $additional ['_query'] ['options'] = 'cart';

            return $this->getProductUrl ( $product, $additional );
        }
        return $objectModelManager->get ( 'Magento\Checkout\Helper\Cart' )->getAddUrl ( $product, $additional );
    }

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product) {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $url = $this->getAddToCartUrl ( $product );
        return [
                'action' => $url,
                'data' => [
                        'product' => $product->getEntityId (),
                        \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $objectModelManager->get ( 'Magento\Framework\Url\Helper\Data' )->getEncodedUrl ( $url )
                ]
        ];
    }

    /**
     * Get product url
     *
     * @param Object $product
     * @param array $additional
     *
     * @return string
     */
    public function getProductUrl($product, $additional = []) {
        if ($this->hasProductUrl ( $product )) {
            if (! isset ( $additional ['_escape'] )) {
                $additional ['_escape'] = true;
            }
            return $product->getUrlModel ()->getUrl ( $product, $additional );
        }

        return '#';
    }

    /**
     * Get the booked dates in array
     */
    public function getBookedDatesArray($productId) {
        $fromDate = $toDate = $daysData = array();
        $bookedDataCollections = $this->objectManager->get('Apptha\Airhotels\Model\Calendar')->getCollection ()->addFieldtoFilter ( 'product_id', $productId );
        /**
         * Get the from and to dates from the airhotels orderorder
         */
        foreach ( $bookedDataCollections as $bookedDataCollection ) {
            $fromDate [] = $bookedDataCollection->getFromdate ();
            $toDate [] = $bookedDataCollection->getTodate ();
        }
        $dateCount = count ( $fromDate );
        /**
         * Get all the dates between the from and to date
         */
        for($i = 0; $i < $dateCount; $i ++) {
            $daysData [] = $this->objectManager->get('Apptha\Airhotels\Controller\Listing\Blockdate')->getAllDatesBetweenTwoDates ( $fromDate [$i], $toDate [$i] );
        }
        /**
         * return the booked dates
         */
        return $this->mergeBlockedDates ( $daysData );
    }

    /**
     * The booking dates has been mergered into a single array
     *
     * @param unknown $daysData
     *
     * @return string[]
     */
    public function mergeBlockedDates($daysData) {
        $result = array ();
        foreach ( $daysData as $sub ) {
            foreach ( $sub as $val ) {
                $val = trim ( $val );
                if (! in_array ( $val, $result )) {
                    $newDate = date ( "m/d/Y", strtotime ( $val ) );
                    $result [] = $newDate;
                }
            }
        }
        return $result;
    }
}
