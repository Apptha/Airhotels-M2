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
namespace Apptha\Airhotels\Block\Message;
class Showmessage extends \Magento\Framework\View\Element\Template{
    /**
      *
      * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
      */
     
     protected $customerReplyCollection;
     protected $_productRepository;
     protected $customerSession;
     protected $customerProfileCollection;
     protected $inbox;
     protected $customerReply;
     protected $inboxCollection;
     /**
      *
      * @param \Magento\Framework\View\Element\Template\Context $context             
      * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory             
      * @param \Apptha\Airhotels\Model\ResourceModel\City\CollectionFactory $cityCollection             
      * @param array $data             
      */
     public function __construct(\Magento\Framework\View\Element\Template\Context $context, 
                \Apptha\Airhotels\Model\ResourceModel\Contacthost\CollectionFactory $inboxCollection, 
                \Apptha\Airhotels\Model\Contacthost $inbox, 
                \Apptha\Airhotels\Model\Customerreply $customerReply, 
                \Apptha\Airhotels\Model\ResourceModel\Customerreply\CollectionFactory $customerReplyCollection, 
                \Apptha\Airhotels\Model\ResourceModel\Customerprofile\CollectionFactory $customerProfileCollection, 
                \Magento\Customer\Model\Customer $customers, 
                \Magento\Customer\Model\Session $customerSession,
                \Magento\Catalog\Model\ProductFactory $productRepository ) {
         
         $this->_inboxCollection = $inboxCollection;
         $this->_inbox = $inbox;
         $this->_customerProfiles = $customerProfileCollection;
         $this->_customerReply = $customerReply;         
         $this->customerSession = $customerSession;
         $this->_customerReplyCollection = $customerReplyCollection;
         $this->_customers = $customers;
         $this->_productloader = $productRepository;
         $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
         
         parent::__construct ( $context );
     }
    /**
      * Getting Inbox details in particular host and product
      * 
      * @return Array
      */
     public function getInboxDetails() {
         /**
           * Getting customer session
           */
          $customerSession = $this->objectManager->get ( 'Magento\Customer\Model\Session' );
          
          /**
           * Checking whether customer is loggedin
           */
         if ($customerSession->isLoggedIn ()) {
         $customerId = $this->customerSession->getId();
         $messageId = $this->getRequest ()->getParam ('id');
         
         
         $customerReadStatus = $this->_customerReplyCollection->create()
                 ->addFieldToFilter('receiver_id', array('eq' => $customerId ))->addFieldToFilter('message_id', array('eq' => $messageId))
             ->addFieldToFilter('is_read', array('eq' => '0'));
         foreach($customerReadStatus as $customerReadStatus){
            $customerReadStatus->setIsRead(1); 
            $customerReadStatus->save();
         }
             
         $hostId = $this->customerSession->getCustomer()->getId();
         $collection = $this->_inboxCollection->create ()->addFieldToFilter ( 'id', $messageId );
         foreach($collection as $_collection){
          if($hostId == $_collection['receiver_id']){            
            $_collection->setReceiverRead(1);
          }          
          if($hostId == $_collection['sender_id']){ 
            $_collection->setSenderRead(1);
          } 
            //Change read status 1 
            $_collection->setReadFlag(1);         
            $_collection->save();
         }
         return $collection->addFieldToFilter(    array('receiver_id', 'reply_message_id'), array( array('eq'=>$hostId), array('eq'=>$hostId)));          
         } else {
              /**
                * Redirect to login page
                */
               $this->_redirect ( 'customer/account/login' );
         }
     }
    /**
      * Getting Customer details
      * @param $customerId
      * @return Array
      */
    public function getCustomer($customerId)
    {
        //Get customer by customerID
        return $this->_customers->load($customerId);
    }
    /**
      * Getting Product details
      * @param $productId
      * @return Array
      */
    public function getProduct($productId)
    {
        return $this->_productloader->create()->load($productId);
    }
    /**
      * Getting Reply message details in particular host and message id.
      * 
      * @return int
      */
     public function getReplyMessageDetails() {
         $messageId = $this->getRequest ()->getParam ('id');
         return $this->_customerReplyCollection->create ()->addFieldToFilter ( 'message_id', $messageId );
     }
    /**
      * Getting Customer Id
      * 
      * @return int
      */
    public function getCustomerId(){
        return $this->customerSession->getId();
    }
    /**
      * Getting Profile Image
      * @param $customerId
      * @return Array
      */
    public function getProfileImage($customerId){
        $_collection = $this->_customerProfiles->create ()->addFieldToFilter ( 'customer_id', $customerId );
        if($_collection->getSize() != 0){
            foreach($_collection as $collection){
                $image = $collection->getProfileimage();
            }
            return $image;
        }
    }
    /**
      * To return the customer profile image
      *
      * @return string
      */
    public function getHostImageUrl() {
          return $this->objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'Airhotels/Customerprofileimage/Resized';
     }
}