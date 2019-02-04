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
namespace Apptha\Airhotels\Controller\Listing;

use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory as CustomOptionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class Basicsave extends \Magento\Framework\App\Action\Action {
     
     /**
      *
      * @var CustomOptionFactory
      */
     protected $resultPageFactory;
     protected $productRepository;
     protected $productFactory;
     protected $customOptionFactory;
     protected $_file;
     const XML_PATH_PRODUCT_APPROVAL = 'airhotels/product/product_approval';
     /**
      * Constructor
      * \Magento\Framework\View\Result\PageFactory $resultPageFactory,
      * \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
      * \Magento\Catalog\Model\ProductFactory $productFactory
      * CustomOptionFactory $customOptionFactory
      */
     public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Framework\Registry $registry, CustomOptionFactory $customOptionFactory, \Magento\Framework\Filesystem\Driver\File $file) {
          $this->resultPageFactory = $resultPageFactory;
          $this->productRepository = $productRepository;
          $this->registry = $registry;
          $this->productFactory = $productFactory;
          $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          $this->scopeConfig = $scopeConfig;
          $this->customOptionFactory = $customOptionFactory;
          $this->_file = $file;
          parent::__construct ( $context );
     }
     
     /**
      * Flush cache storage
      */
     public function execute() {
          $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          $productApproval = $this->scopeConfig->getValue ( static::XML_PATH_PRODUCT_APPROVAL, $storeScope );
          $customerSession = $this->objectManager->get ( 'Magento\Customer\Model\Session' );
          $customerId = $customerSession->getCustomer ()->getId ();
          $productId = $customerSession->getCurrentExperienceId ();
          $selectedTab = $this->getRequest ()->getParam ( 'selected_tab' );
          $productTypeId = 'booking';
          $productData = $this->getRequest ()->getParam ( 'product' );
          $categoryIds =  $productData['category_ids'];
          $propertyAddress = $productData ['propertyaddress'];
          $latitude = $productData ['latitude'];
          $longitude = $productData ['longitude'];
          $city = $productData ['city'];
          $state = $productData ['state'];
          $country = $productData ['country'];
          $description = $productData ['description'];          
          $accommodateMinimum = $productData ['accommodate_minimum'];
          $accommodateMaximum = $productData ['accommodate_maximum'];
          $name = $productData ['name'];
          $price = $productData ['price'];
          $random = rand ( 1, 100000000000 );
          $sku = rand ( 1, $random );
          
          if (! empty ( $productId )) {
               $product = $this->objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $productId );
               
               $product->setPropertyaddress ( $propertyAddress );
               $product->setLatitude ( $latitude );
               $product->setLongitude ( $longitude );
               $product->setCity ( $city );
               $product->setState ( $state );
               $product->setCountry ( $country );
               $product->setDescription ( $description );              
               $product->setAccommodateMinimum ( $accommodateMinimum );
               $product->setAccommodateMaximum ( $accommodateMaximum );
               
               $product->setPrice ( $price );
               $product->setName ( $name );
               $product->setUserId ( trim ( $customerId ) );
          } else {
               $product = $this->productFactory->create ();
          }
          if (empty ( $productId )) {
               $product->setTypeId ( $productTypeId );
               $product->setAttributeSetId ( 4 );
               $product->setCreatedAt ( strtotime ( 'now' ) );
               $product->setPropertyaddress ( $propertyAddress );
               $product->setLatitude ( $latitude );
               $product->setLongitude ( $longitude );
               $product->setCity ( $city );
               $product->setState ( $state );
               $product->setCountry ( $country );
               $product->setDescription ( $description );              
               $product->setAccommodateMinimum ( $accommodateMinimum );
               $product->setAccommodateMaximum ( $accommodateMaximum );
               $product->setSku ( $sku );
               $product->setPrice ( $price );
               $product->setName ( $name );
               $product->setUserId ( trim ( $customerId ) );
               $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
          }
          $id = null;
          $manager = $this->objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' );
          $store = $manager->getStore ( $id );
          $websiteId = $store->getWebsiteId ();
          $product->setWebsiteIds ( array (
                    $websiteId 
          ) );
          
          $product->setName ( $productData ['name'] );
          if ($productTypeId == 'booking') {
               $product->setStockData ( array (
                         'use_config_manage_stock' => 0,
                         'is_in_stock' => 1,
                         'manage_stock' => 0,
                         'use_config_notify_stock_qty' => 0 
               ) );
          }
          
          $customAttributes = $this->getRequest ()->getParam ( 'custom_attributes' );
          $this->setProductData ( $product, $categoryIds, $productData, $customAttributes );
          
          if ($productApproval == 1) {
               $product->setPropertyApproved ( 1 );
               $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
          } else {
               $product->setPropertyApproved ( 0 );
              $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
          }
          
          $product->save ();
          $customerSession->setCurrentExperienceId ( $product->getId () );
          if (! empty ( $productId )) {
               $product->save ();
          } else {
               $productCollection = $this->objectManager->create ( 'Magento\Catalog\Model\ResourceModel\Product\Collection' )->addAttributeToFilter ( 'url_key', $productData ['name'] );
               $productCollectionData = $productCollection->getData ();
               $urlKeyCount = count ( $productCollectionData );
               if ($urlKeyCount >= 1) {
                    $product->setUrlKey ( $productData ['name'] . rand ( 1, 10000 ) );
               }
               $product->save ();
          }
          //for sending new property email
           if (empty ( $productId )) {
            $this->sendNewPropertyEmail($product);
           }
            //for sending  property awaiting  approval
           if (!empty ( $productId ) && $product->getPropertyApproved()==0) {
            $this->sendAwaitingApprovalEmail($product);
           }
          $this->_eventManager->dispatch ( 'controller_action_catalog_product_save_entity_after', [ 
                    'controller' => $this,
                    'product' => $product 
          ] );
          if (empty ( $selectedTab )) {
               $selectedTab = "photos";
          }
          return $this->_redirect ( '*/listing/form/step/' . $selectedTab );
     }
     
     /**
      * To set product data
      *
      * @param object $product             
      * @param array $categoryIds             
      * @param array $productData             
      * @param float $nationalShippingAmount             
      * @param float $internationalShippingAmount             
      * @param array $customAttributes             
      *
      * @return object
      */
     public function setProductData($product, $categoryIds, $productData, $customAttributes) {
          $product->setCategoryIds ( $categoryIds );
          
          $product->setVisibility ( \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH );
          
          if (! empty ( $customAttributes )) {
               $product = $this->objectManager->get ( 'Apptha\Airhotels\Block\Booking\Customattributes' )->addCustomAttributes ( $product, $customAttributes, $productData );
          }
          
          $product->setMetaKeyword ( $productData ['name'] );
          $product->setMetaDescription ( $productData ['name'] );
          return $product;
     }
     
     /**
      * Update stock data for product
      *
      * @param int $productId             
      * @param array $productData             
      * @return void
      */
     public function updateStockDataForProduct($productId, $productData) {
          $stockData = $this->objectManager->get ( 'Magento\CatalogInventory\Api\Data\StockItemInterface' )->load ( $productId, 'product_id' );
          $stockData->setQty ( $productData ['quantity_and_stock_status'] ['qty'] );
          $stockData->setIsInStock ( $productData ['quantity_and_stock_status'] ['is_in_stock'] );
          $stockData->save ();
     }
    /**
     * Send new property email
     *
     * @param object $product
     * @return void
     */
    public function sendNewPropertyEmail($property) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $propertyNameVal = $property->getName ();
        $productUrlVal = $property->getProductUrl ();
        $userId = $property->getUserId ();
        $customer = $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $userId );
        $recipient = $customer->getEmail ();
        $customerName = $customer->getName ();
    
        /* Here we prepare data for our email  */  
          $admin = $objectManager->get ( 'Apptha\Airhotels\Helper\Data' );
          /**
           * Assign admin details
           */
          $adminName = $admin->getAdminName ();
          $adminEmail = $admin->getAdminEmail ();
          $receiverInfo = [
                'name' => $adminName,
                'email' => $adminEmail,
            ];
            
            /* Receiver Detail  */
            $senderInfo = [
                'name' =>$customerName,
                'email' =>$recipient
            ];
            $templateId = 'airhotels_product_new_booking_template';
            $emailTempVariables = (array (
                'ownername' => $adminName,
                'pname' => $propertyNameVal,
                'purl' => $productUrlVal,
                'cname' => $customerName 
            ));
            /* We write send mail function in helper because if we want to
             use same in other action then we can call it directly from helper */
            
            /* call send mail method from helper or where you define it*/
            $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,$templateId
            );
    
    }
    /**
     * Send new property awaiting for approval email
     *
     * @param object $product
     * @return void
     */
    public function sendAwaitingApprovalEmail($property) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $propertyNameVal = $property->getName ();
        $userId = $property->getUserId ();
        $customer = $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $userId );
        $recipient = $customer->getEmail ();
        $customerName = $customer->getName ();
    
        /* Here we prepare data for our email  */
            /**
             * Getting the Property templeId value
             */
          $admin = $objectManager->get ( 'Apptha\Airhotels\Helper\Data' );
          /**
           * Assign admin details
           */
          $adminName = $admin->getAdminName ();
          $adminEmail = $admin->getAdminEmail ();
          $templateId = 'airhotels_product_host_booking_approval_template';
          /* Sender Detail  */
            $senderInfo = [
                'name' =>$customerName,
                'email' =>$recipient
            ];
         /* Receiver Detail  */
            $receiverInfo = [
                'name' => $adminName,
                'email' => $adminEmail,
            ];
             $emailTempVariables = (array (
                'ownername' => $adminName,
                'pname' => $propertyNameVal,
                'product_url'=>$property->getProductUrl(),
                'cname' => $customerName 
            ));
            
            /* We write send mail function in helper because if we want to
             use same in other action then we can call it directly from helper */
            
            /* call send mail method from helper or where you define it*/
            $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,$templateId
            );
    
    }

}