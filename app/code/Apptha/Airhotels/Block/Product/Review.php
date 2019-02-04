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
namespace Apptha\Airhotels\Block\Product;

class Review extends \Magento\Framework\View\Element\Template{
             
     protected $reviewFactory;
     protected $request;
     protected $customerProfile;
     
     /**
      * @param \Magento\Framework\View\Element\Template\Context $context
      * @param \Magento\Store\Model\StoreManagerInterface $storeManager
      * @param \Magento\Review\Model\ReviewFactory $reviewFactory
      * @param \Magento\Framework\App\Request\Http $request
      */
     public function __construct(\Magento\Framework\View\Element\Template\Context $context,\Magento\Review\Model\ReviewFactory $reviewFactory,\Apptha\Airhotels\Model\Customerprofile $customerProfile, \Magento\Framework\App\Request\Http $request) 
     {
          $this->reviewFactory = $reviewFactory;
          $this->request = $request;
          $this->_customerProfile = $customerProfile;
          
          parent::__construct ( $context);
     }
     
     /**
      * Prepare layout to rendar the pagination block
      */
     public function _prepareLayout()
     {
          parent::_prepareLayout();
          $productId = $this->request->getParam('id');
               
               
          if ($this->getReviewPagination($productId)) {
               /**
                *
                * @var \Magento\Theme\Block\Html\Pager
                */
               $pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'review.pager' );
               $pager->setLimit ( 5 )->setShowPerPage(true)->setCollection ( $this->getReviewPagination ($productId) );
               $this->setChild ( 'pager', $pager );
     
               return $this;
          }
     }
     
     /**
      * Get review details based on the product id
      * 
      * @param int $productId product id
      * 
      * @return array
      */
     public function getReviewDetails($productId){
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $rating = $objectManager->get("Magento\Review\Model\ResourceModel\Review\CollectionFactory");
          return $rating->create()
          ->addStatusFilter(
                    \Magento\Review\Model\Review::STATUS_APPROVED
                    )->addEntityFilter(
                              'product',
                              $productId
                              )->setDateOrder()->addRateVotes ();
     }
     
     
     /**
      * Get rating summary for the product.
      * 
      * @param array $product Product object
      * 
      * @return int
      */
     public function getRatingSummary($product)
     {
          $storeId = $this->_storeManager->getStore ()->getId ();
          $this->reviewFactory->create ()->getEntitySummary ( $product, $storeId );
          
          return $product->getRatingSummary ()->getRatingSummary ();
     }
    /**
      * To return the customer profile image
      *
      * @return string
      */
     public function getHostData($customerId) {
          return $this->_customerProfile->load ( $customerId, 'customer_id' );
     }
          
     /**
      * display seller construct
      *
      * @return void
      */
     public function getReviewPagination($productId) {
          /** get values of current page */
          $page = ($this->request->getParam('p'))? $this->request->getParam('p') : 1;
          /**  get values of current limit */
          $pageSize =  ($this->request->getParam('limit'))? $this->request->getParam('limit') : 5;
          
          $productId = ($productId) ? $productId :$this->request->getParam('id'); 
          
          return $this->getReviewDetails($productId)->setPageSize ( $pageSize )->setCurPage ( $page );
     }
     
     /**
      * Get pagination html
      * 
      * @return object
      */
     public function getPagerHtml()
     {
          return $this->getChildHtml('pager');
     }
     /**
      * To return the customer profile image
      *
      * @return string
      */
     public function getHostImageUrl() {
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          return $objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'Airhotels/Customerprofileimage/Resized';
     }
     /**
      * reduce review description length
      *
      * @param string $reviewDetails             
      *
      * @return multitype:string boolean
      */
     public function getReviewDescription($reviewDetails) {
          $reviewDescription = substr ( $reviewDetails, 0, 20 );
          if (strlen ( $reviewDetails ) > 20) {
               $reviewDescription .= '...';
          }
          return $reviewDescription;
     }
    
}