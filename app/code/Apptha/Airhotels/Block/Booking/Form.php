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
namespace Apptha\Airhotels\Block\Booking;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Resource\Product\CollectionFactory;
use Zend\Form\Annotation\Object;
class Form extends \Magento\Framework\View\Element\Template
{
    protected $_orderCollectionFactory;
    
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Eav\Model\Config $eavConfig, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attributeSet, \Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState, CategoryRepositoryInterface $categoryRepository, \Magento\Customer\Model\Session $customerSession, \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory , array $data = []) {
        parent::__construct ( $context, $data );
        $this->storeManager = $context->getStoreManager();
        $this->attributeSet = $attributeSet;
        $this->product = $product;
        $this->eavConfig = $eavConfig;
        $this->customerSession = $customerSession;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->categoryRepository = $categoryRepository;
        $this->_orderCollectionFactory = $orderCollectionFactory;
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
     * Get Attribute set datas
     *
     * @return array
     */
    public function getAttributeSet() {
        return $this->attributeSet->toOptionArray ();
    }

    /**
     * Retrieve current store categories
     *
     * @param bool|string $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     *
     * @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true) {
        return $this->objectManager->get ( 'Magento\Catalog\Helper\Category' )->getStoreCategories ( 'name', $asCollection, $toLoad );
    }

    /**
     * Sort the categories in alphabatical order
     *
     * @return Object
     */
    public function alphabaticalOrder($categories, $catChecked) {
        $categoryName = array ();
        foreach ( $categories as $category ) {
            if (! $category->getIsActive ()) {
                continue;
            }
            /**
             * Get category id
             */
            $catagoryId = $category->getId ();
            
            /**
             * Checking for have children category or not
             */
            if ($category->hasChildren ()) {
                $catagoryId = $category->getId () . 'sub';
            }
            $categoryName [$catagoryId] = $category->getName ();
        }
        /**
         * Sort category name
         */
        asort ( $categoryName );
        return $this->objectManager->get ( 'Apptha\Airhotels\Helper\Data' )->showCategoriesTree ( $categoryName, $catChecked );
    }

    /**
     * Get ajax category tree action url
     *
     * @return string
     */
    public function getCategoryTreeAjaxUrl() {
        return $this->getUrl ( 'airhotels/listing/category' );
    }

    /**
     * Get current currency symbol
     */
    public function getCurrentCurrencySymbol(){

        $currencyCurrencyCode = $this->objectManager->get('\Magento\Framework\Pricing\PriceCurrencyInterface');


        return $currencyCurrencyCode->getCurrency()->getCurrencySymbol();
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
     * Get product data collection
     *
     * Passed the product id to get product details
     *
     * @param int $productId
     * Return product details as array
     * @return array
     */
    public function getProductData($productId, $storeId) {
        /**
         * Getting product collection data
         */
        /**
         * load and return the products
         * Based on the product entity id
         */
        return $this->product->setStoreId ( $storeId )->load ( $productId );
    }
    /**
     * Get custom attributes ajax url
     *
     * @return string
     */
    public function getCustomAttributesUrl() {
        return $this->getUrl ( 'airhotels/listing/attributes' );
    }

    /**
     * Get ajax image upload action url
     *
     * @return string
     */
    public function getImageUploadAjaxUrl() {
        return $this->getUrl ( 'booking/listing/imageupload' );
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
     * Get customer order
     *
     * @return string
     */
    public function getCustomerOrder($productIds) {
        $customerCollection = $this->customerSession;
        $customerId = $customerCollection->getCustomerId();
        $collection = $this->objectManager->get ('Apptha\Airhotels\Model\Hostorder')->getCollection();
        $collection->addFieldToFilter('customer_id',$customerId);
        $collection->addFieldToFilter('order_status','complete');
        $collection->addFieldToFilter('entity_id',$productIds);
        return $collection;
    }

}
