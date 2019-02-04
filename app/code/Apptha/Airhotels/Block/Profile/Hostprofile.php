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
class Hostprofile extends \Magento\Framework\View\Element\Template{
    /**
     * Get customer profile
     * @var object
     */
    protected $_hostProfile;
    /**
     * Get customer session
     * @var object
     */
    protected $hostSession;
    /**
     * Get verify collection
     * @var object
     */
    protected $verifyhosts;

    /**
     *  get Property Collection
     *  @var object
     */
    protected $propertyCollection;
    /**
     *  get Property Review Collection
     *  @var object
     */
    protected $propertyReviewCollection;
    /**
     * get review Collection
     * @var object
     */
    protected $reviewCollectionFactory;
    /**
     * __construct to load the model collection
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Apptha\Airhotels\Model\Customerprofile $custoemrProfile
     * @param \Apptha\Airhotels\Model\Verifyhost $verifyhost
     * @param Session $hostSession
     * @param Customer $customerModel
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \Apptha\Airhotels\Model\Customerprofile $_hostProfile,
                  \Apptha\Airhotels\Model\Verifyhost $verifyhosts,
            \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
            \Magento\Review\Model\ReviewFactory $reviewFactory,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $propertyCollection,
            \Magento\Catalog\Model\Product $propertyReviewCollection,
            \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory,
                  Session $hostSession,
                  Customer $customerModel) {
                $this->hostSession = $hostSession;
                $this->verifyhosts = $verifyhosts;
                $this->_hostProfile = $_hostProfile;
                $this->_customer = $customerModel;
                $this->_wishlistRepository= $wishlistRepository;
                $this->_reviewFactorys = $reviewFactory;
                $this->_propertyCollection =$propertyCollection;
                $this->_propertyReviewCollection= $propertyReviewCollection;
                $this->_reviewCollectionFactory=$reviewCollectionFactory;
                parent::__construct ( $context);
    }

  /**
     * Prepare layout for listings
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        parent::_prepareLayout ();
        $pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'airhotels.hostprofile.pager' );
        $pager->setAvailableLimit(array(
                10 => 10,
                20 => 20,
                50 => 50
            ))->setShowAmounts ( false )->setCollection ( $this->getMyListings() );
        $this->setChild ( 'pager', $pager );
        $this->getMyListings() ->load();
        return $this;
    }

    /**
     * To get the current customer collection
     * @return object
     */
    public function getHostDetails(){
     $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
     return $objectModelManager->get('Magento\Customer\Model\Customer')->load($this->getRequest()->getParam('id'));
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
    public function getHostProfileData(){
        $customerData=$this->getHostDetails();
        return $this->_hostProfile->load ( $customerData->getId(), 'customer_id' );
    }

    /**
     * To get the Host Verified collection
     * @return object
     */
    public function getHostsVerified(){
           $customerData=$this->getHostDetails();
           return $this->verifyhosts->load ( $customerData->getId(), 'host_id' );
    }

    /**
     * To return the customer profile image
     * @return string
     */
    public function getMyListings(){
      /***get values of current page*/
      $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
      /**get values of current limit*/
      $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 10;
        $customerData=$this->getHostDetails();
         return $this->_propertyCollection->create()
         ->addAttributeToSelect('*')->addFieldToFilter('status',1)->addFieldToFilter('property_approved',1)
         ->addAttributeToFilter('user_id',$customerData->getId())->setPageSize($pageSize)->setCurPage($page);
    }
    /**
     * To return the rating summary
     * @return string
     */
    public function getRatingSummarys($products)
    {
        $this->_reviewFactorys->create()
        ->getEntitySummary($products, $this->_storeManager->getStore()->getId());
        return $products->getRatingSummary()->getRatingSummary();
    }
    /**
     * To return the rating summary
     * @return string
     */
    public function getReviewDetails($sku)
    {
        $product = $this->_propertyReviewCollection->loadByAttribute ( 'sku', $sku );
        $storeManager = $this->_storeManager;
        $currentStoreId = $storeManager->getStore ()->getId ();
       return $this->_reviewCollectionFactory->create ()->addStoreFilter ( $currentStoreId )->addStatusFilter ( \Magento\Review\Model\Review::STATUS_APPROVED )->addEntityFilter ( 'product', $product->getId () )->setDateOrder ();
    }


    /**
     * To return the profile image
     * @return string
     */
    public function gethostProfileImage($reviewCollection)
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
}
