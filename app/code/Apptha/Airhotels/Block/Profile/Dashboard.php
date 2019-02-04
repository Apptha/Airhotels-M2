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
use Magento\Customer\Model\Customer;
class Dashboard extends \Magento\Framework\View\Element\Template{
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
     * Get verify collection
     * @var object
     */
    protected $verifyhost;
    /**
     * Get customer data
     * @var object
     */
    protected $_customer;
    /**
     * __construct to load the model collection
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Apptha\Airhotels\Model\Customerprofile $custoemrProfile
     * @param \Apptha\Airhotels\Model\Verifyhost $verifyhost
     * @param Session $customerSession
     * @param Customer $customerModel
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \Apptha\Airhotels\Model\Customerprofile $customerProfile,
                  \Apptha\Airhotels\Model\Verifyhost $verifyhost,
            \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
            \Magento\Review\Model\ReviewFactory $reviewFactory,
                  Session $customerSession,
                  Customer $customerModel) {
                $this->customerSession = $customerSession;
                $this->verifyhost = $verifyhost;
                $this->_customerProfile = $customerProfile;
                $this->_customer = $customerModel;
                $this->_wishlistRepository= $wishlistRepository;
                $this->_reviewFactory = $reviewFactory;
                parent::__construct ( $context);
    }
    /**
     * To get the current customer collection
     * @return object
     */
    public function getCustomerDetails(){
        return $this->customerSession->getCustomer();
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
     * To get the current customer profile data
     * @return object
     */
    public function getCustomerProfileData(){
        $customerData=$this->getCustomerDetails();
        return $this->_customerProfile->load ( $customerData->getId(), 'customer_id' );
    }

    /**
     * Load the perticular customer data
     * @return object
     */
    public function getCustomerData(){
        $customerData=$this->getCustomerDetails();
        return $this->_customer->load ( $customerData->getId() );
    }
    /**
     * To get the edit profile url
     * @return string
     */
    public function getEditProfileUrl(){
        return $this->getUrl('booking/profile/edit');
    }

    /**
     * To get the help url
     * @return string
     */
    public function getHelpUrl(){
        return $this->getUrl('help');
    }

    /**
     * To get the Host Verified collection
     * @return object
     */
    public function getHostVerified(){
           $customerData=$this->getCustomerDetails();
           return $this->verifyhost->load ( $customerData->getId(), 'host_id' );
    }

    /**
     * To get the Trust and Verify URL
     * @return object
     */
    public function getTrustandVerifyUrl(){
           return $this->getUrl('booking/profile/verification');
    }
    /**
    * To get the current user wishlist
    * @return object
    */
    public function getWishlist(){
        $customerData=$this->getCustomerDetails();
        $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($customerData->getId(), true);
        return $wishlist->getItemCollection();
    }
    /**
     * To return the customer profile image
     * @return string
     */
    public function getMyListing(){
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerData=$this->getCustomerDetails();
        $collection = $objectModelManager->get ( '\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory' );
        $collection = $collection->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('user_id',$customerData->getId());
        return $collection;
    }
    /**
     * To return the rating summary
     * @return string
     */
    public function getRatingSummary($product)
    {
        $this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        return $product->getRatingSummary()->getRatingSummary();
    }
    /**
     * To return the rating summary
     * @return string
     */
    public function getReviewDetails($sku)
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $product = $_objectManager->create ( "Magento\Catalog\Model\Product" )->loadByAttribute ( 'sku', $sku );
        $storeManager = $_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' );
        $currentStoreId = $storeManager->getStore ()->getId ();
        $rating = $_objectManager->get ( "Magento\Review\Model\ResourceModel\Review\CollectionFactory" );
        return $rating->create ()->addStoreFilter ( $currentStoreId )->addStatusFilter ( \Magento\Review\Model\Review::STATUS_APPROVED )->addEntityFilter ( 'product', $product->getId () )->setDateOrder ();
    }
    /**
     * To return the profile image
     * @return string
     */
    public function getProfileImage($reviewCollection)
    {
      $_objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
      if(!$reviewCollection->getCustomerId()){
      return $_objectManager->get ( 'Apptha\Airhotels\Model\Customerprofile' )->load($reviewCollection->getCustomerId(),'customer_id');
      } else {
          return;
      }
    }
    /**
     * Get Manage product pager html
     *
     * @return string
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }
    /**
     * Prepare layout for listings
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        parent::_prepareLayout ();
        /**
         *
         * @var \Magento\Theme\Block\Html\Pager
         */
        $pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'airhotels.product.pager' );
        $pager->setAvailableLimit(array(
                10 => 10,
                20 => 20,
                50 => 50
            ))->setShowAmounts ( false )->setCollection ( $this->getRatingSummaryCollection() );
        $this->setChild ( 'pager', $pager );
        $this->getMyListing() ->load();
        return $this;
    }
    /**
     * To return the rating summary
     * @return string
     */
    public function getRatingSummaryCollection()
    {
      /**
      * get product id
      */
      $productIds = Array();
      foreach($this->getMyListing() as $product)
      {
        $productIds[]=$product->getEntityId();
      }
      //get values of current page
      $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
      //get values of current limit
      $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 10;
      $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
      $collection = $objectModelManager->get ( '\Magento\Review\Model\ReviewFactory' );
      $collection = $collection->create()->getCollection();
      $collection->addFieldToFilter('entity_pk_value',array('in'=>$productIds));
      $collection->addFieldToFilter('status_id',1);
      $collection->setPageSize($pageSize);
      $collection->setCurPage($page);
      return $collection;
    }
    /**
     * To return the get Customer
     * @return array
     */
    public function getCustomer($customerId)
    {
      $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
      return $objectModelManager->get ( '\Magento\Customer\Model\Customer')->load($customerId);
    }
}
