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
namespace Apptha\Airhotels\Controller\Contacthost;
use Apptha\Airhotels\Model\ContacthostFactory;
use Magento\Framework\Controller\ResultFactory;

class Contacthost extends \Magento\Framework\App\Action\Action {
    
     protected $_pageFactory;
     protected $contactHost;
     protected $request;
     protected $_contactHost;
     protected $_productRepository;
     protected $customerSession;
     protected $_customers;

    public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\App\Request\Http $request,
    \Magento\Customer\Model\Session $customerSession,
    \Magento\Customer\Model\Customer $customers,
    \Magento\Catalog\Model\ProductFactory $productRepository,
    ContacthostFactory $_contactHost
    )
    {
    
    $this->request = $request;
    $this->_customers = $customers;
    $this->_contactHost = $_contactHost;
    $this->customerSession = $customerSession;
    $this->_productloader = $productRepository;
    $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
    return parent::__construct($context);
    }
     /**
      * Flush cache storage
      */
     public function execute() {
         /**
           * Getting customer session
           */
          $customerSession = $this->objectManager->get ( 'Magento\Customer\Model\Session' );
          
          /**
           * Checking whether customer is loggedin
           */
         if ($customerSession->isLoggedIn ()) {
             $customerId = $this->customerSession->getCustomer()->getId();
             $hostId = $this->getRequest ()->getParam ('host_id');
             $productId = $this->getRequest ()->getParam ('product_id');
             $contactHost = $this->_contactHost->create();
             $checkin = $this->objectManager->get('Apptha\Airhotels\Helper\Dateformat')->searchDateFormat($this->getRequest ()->getParam ('from'));
             $checkout = $this->objectManager->get('Apptha\Airhotels\Helper\Dateformat')->searchDateFormat($this->getRequest ()->getParam ('to')); 
             $email = $this->getRequest ()->getParam ('email');
             $phoneno = $this->getRequest ()->getParam ('phoneno');
             $message = $this->getRequest ()->getParam ('contact_host_desc');
             $guests = $this->getRequest ()->getParam ('number_of_guests');

             $contactHost->setCheckin ( $checkin );
             $contactHost->setCheckout ( $checkout );
             $contactHost->setEmail ( $email );
             $contactHost->setPhoneNo ( $phoneno );
             $contactHost->setMessage ( $message );
             $contactHost->setGuest ( $guests );
             $contactHost->setSenderId ( $customerId );
             $contactHost->setReceiverId ( $hostId );
             $contactHost->setProductId ( $productId );
             $contactHost->setReadFlag ( 0 );
             $contactHost->setSenderRead ( 0 );
             $contactHost->setReceiverRead ( 0 );
             $contactHost->setReplyMessageId ( 0 );
             $contactHost->setCreatedAt ( strtotime ( 'now' ) );
             $contactHost->save ();
             
             //Get product Name
             $product = $this->_productloader->create()->load($productId);
             $productName = $product->getName();
                //loading host details
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
                //Sender Details
                $customerSession = $this->objectManager->get('Magento\Customer\Model\Session');
               
                //Receiver Details
                $receiver = $this->getReceiverDetails($hostId);
                $receiverName = $receiver->getName();
                $receiverEmail = $receiver->getEmail();
                
                $admin = $objectManager->get('Apptha\Airhotels\Helper\Data');
                /**
                 * Assign admin details
                 */
                $adminName = $admin->getAdminName();
                $adminEmail = $admin->getAdminEmail();
                $templateId = 'airhotels_contact_host';
                /* Sender Detail */
                $senderInfo = [
                    'name' => $adminName,
                    'email' => $adminEmail
                ];
                /* Template variables Detail */
                $emailTempVariables = (array(
                    'name' => $receiverName,
                    'message' => $message,
                    'guests' => $guests,
                    'check_in' => $checkin,
                    'check_out' => $checkout,
                    'product_name' => $productName
                ));
                /* Receiver Detail */
                $receiverInfo = [
                    'name' => $receiverName,
                    'email' => $receiverEmail
                ];
                
                /* call send mail method from helper or where you define it */
                $objectManager->get('Apptha\Airhotels\Helper\Email')->contacthostMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId, $receiverEmail);
                $this->messageManager->addSuccess(__('Message sent successfully to the host.'));
                //Redirect same page
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
         } else {
              /**
                * Redirect to login page
                */
               $this->_redirect ( 'customer/account/login' );
         }
         
     }
    /**
     * To get the customer details
     * @param $receiverId
     * @return Array
     */
    public function getReceiverDetails($receiverId) {
        return $this->_customers->load($receiverId);
    }
}