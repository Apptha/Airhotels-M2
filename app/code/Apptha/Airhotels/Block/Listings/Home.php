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
namespace Apptha\Airhotels\Block\Listings;

class Home extends \Magento\Framework\View\Element\Template {
     
     /**
      *
      * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
      * @var \Apptha\Airhotels\Model\ResourceModel\City\CollectionFactory
      * @var \\Magento\Catalog\Api\ProductRepositoryInterfaceFactory
      * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
      * @var \Apptha\Airhotels\Model\ResourceModel\Uploadvideo\CollectionFactory
      */
     protected $collectionFactory;
     protected $cityCollection;
     protected $product;
     protected $categoryCollectionFactory;
     protected $banner;
     
     /**
      *
      * @param \Magento\Framework\View\Element\Template\Context $context             
      * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory             
      * @param \Apptha\Airhotels\Model\ResourceModel\City\CollectionFactory $cityCollection             
      * @param \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $product             
      * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,             
      * @param array $data             
      */
     public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $collectionFactory, \Apptha\Airhotels\Model\ResourceModel\City\CollectionFactory $cityCollection, \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $product, \Apptha\Airhotels\Model\ResourceModel\Uploadvideo\CollectionFactory $banner, \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory, array $data = []) {
          $this->_collectionFactory = $collectionFactory;
          $this->_cityCollection = $cityCollection;
          $this->_productFactory = $product;
          $this->_bannerFactory = $banner;
          $this->_categoryCollectionFactory = $categoryCollectionFactory;
          parent::__construct ( $context, $data );
     }
     /**
      * Getting most viewed products
      * 
      * @return Array
      */
     public function getCollection() {
          $currentStoreId = $this->_storeManager->getStore ()->getId ();
          return $this->_collectionFactory->create ()->addAttributeToSelect ( '*' )->addFieldToFilter ( 'status', 1 )->setStoreId ( $currentStoreId )->addStoreFilter ( $currentStoreId )->setOrder('created_at','DESC')->setPageSize ( 10 );
     }
     /**
      * Getting city by product collection
      * 
      * @return Array
      */
     public function getProductCityList() {
          $cityList = Array ();
          $currentStoreId = $this->_storeManager->getStore ()->getId ();
          $collection = $this->_collectionFactory->create ()->addAttributeToSelect ( '*' )->addFieldToFilter ( 'status', 1 )->setStoreId ( $currentStoreId )->addStoreFilter ( $currentStoreId );
          foreach ( $collection as $product ) {
               $cityList [] = $this->getProduct ( $product->getId () )->getCity ();
          }
          return $cityList;
     }
     /**
      * Getting city collection
      * 
      * @return Array
      */
     public function getCityCollection() {
          return $this->_cityCollection->create ()->addFieldToFilter ( 'status', 1 )->addFieldToFilter ( 'name', array (
                    'in' => $this->getProductCityList () 
          ) )->distinct ( true );
     }
     /**
      * Getting media url
      * 
      * @return String
      */
     public function getMediaUrl() {
          return $this->_storeManager->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
     }
     /**
      * Getting city wise products collection
      * 
      * @return Array
      */
     public function getProductionByCity($city) {
          $currentStoreId = $this->_storeManager->getStore ()->getId ();
          $collection = $this->_collectionFactory->create ()->addAttributeToSelect ( '*' )->addFieldToFilter ( 'status', 1 )->setStoreId ( $currentStoreId )->addStoreFilter ( $currentStoreId );
          if ($city) {
               $collection->addAttributeToFilter ( 'city', $city )->setPageSize ( 10 );
          }
          return $collection;
     }
     /**
      * Getting products data
      * 
      * @return Array
      */
     public function getProduct($product_id) {
          return $this->_productFactory->create ()->getById ( $product_id );
     }
     /**
      * Getting category collection
      * 
      * @return Array
      */
     public function getCategoryCollection() {
          $collection = $this->_categoryCollectionFactory->create ();
          $collection->addAttributeToSelect ( '*' )->addIsActiveFilter ()->addFieldToFilter ( 'parent_id', array (
                    'nin' => 1 
          ) );
          return $collection;
     }
     /**
      * Getting production collection by category
      * 
      * @return Array
      */
     public function getProductCollectionByCategory($catagory_id) {
          $currentStoreId = $this->_storeManager->getStore ()->getId ();
          $collection = $this->_collectionFactory->create ()->addAttributeToSelect ( '*' )->addFieldToFilter ( 'status', 1 )->setStoreId ( $currentStoreId )->addStoreFilter ( $currentStoreId );
          $collection->addCategoriesFilter ( array (
                    'in' => $catagory_id 
          ) );
          $collection->setPageSize ( 10 );
          return $collection;
     }
     /**
      * Getting most viewed products
      * 
      * @return Array
      */
     public function toCurrentCurrency($price) {
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $priceHelper = $objectManager->get('Magento\Framework\Pricing\Helper\Data');
          return $priceHelper->currency($price, true, false);
     } 
     /**
      * Getting most viewed products
      * 
      * @return Array
      */
     public function getPagebanner() {
          $bannerImage = $this->_bannerFactory->create ()->addFieldToFilter ( 'status', 1 )->getFirstItem ()->getImageUrl ();
          if (! $bannerImage) {
               $bannerImage = "/apptha.jpg";
          }
          return $this->getMediaUrl () . 'Airhotels/Banner' . $bannerImage;
     }
}