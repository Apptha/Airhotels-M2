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
namespace Apptha\Airhotels\Controller\Adminhtml\Payout;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;

/**
 * Class used to save the transaction history information
**/
class Save extends \Magento\Framework\App\Action\Action{
     
     /**
      * Load the page collection
      * @var object
      */
     protected $resultPageFactory;
     /**
      * Get current customer details
      * @var object
      */
     protected $customerSession;
     
     /**
      * __construct to load the model collection
      * @param Context $context
      * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
      * @param \Magento\Framework\Filesystem $filesystem
      * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
      * @param Session $customerSession
      */
     public function __construct(Context $context,
               \Magento\Framework\View\Result\PageFactory $resultPageFactory,
               Session $customerSession){
                    $this->resultPageFactory = $resultPageFactory;
                    $this->customerSession = $customerSession;
                    
                    parent::__construct($context);
     }
     
     /**
      * Update the transaction history details
      */
    public function execute() {
        $getId = $this->getRequest ()->getParam ( 'id' );
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $hostOrderDetails = $objectManager->get ( 'Apptha\Airhotels\Model\Hostorder' )->load ($getId, 'id' );
        $paymentStatus=$this->getRequest ()->getParam ( 'payment_status' );
        if($hostOrderDetails->getId()){
            $hostOrderDetails->setPaymentStatus($paymentStatus);
             $hostOrderDetails->setPaymentComment($this->getRequest ()->getParam ( 'payment_comment' ));
             $hostOrderDetails->save();
             if($paymentStatus==3){
                 $this->sendPaidEmailToHost($hostOrderDetails);
             }
             $this->messageManager->addSuccess ( __ ( 'Transaction details updated.' ) );
        }else{
             $this->messageManager->addSuccess ( __ ( 'This order no longer exists.' ) );
        }
        
        $this->_redirect ( 'airhotelsadmin/orders/index' );
    }
    /**
     * Sending paid email to host
     * @param object $hostOrder
     * 
     * return void
     */
    public function sendPaidEmailToHost($hostOrder){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // loading host details
        $host = $objectManager->get('Magento\Customer\Model\Customer')->load($hostOrder->getHostId());
        /**
         * Property Email Owner
         */
        $recipient = $host->getEmail();
        /**
         * Property Email Owner
         */
        $hostName = $host->getName();
        $admin = $objectManager->get('Apptha\Airhotels\Helper\Data');
        /**
         * Assign admin details
         */
        $adminName = $admin->getAdminName();
        $adminEmail = $admin->getAdminEmail();
        $templateId = 'airhotels_admin_paid_money_host';
        /* Sender Detail */
        $senderInfo = [
            'name' => $adminName,
            'email' => $adminEmail
        ];
        /* Receiver Detail */
        $receiverInfo = [
            'name' => $hostName,
            'email' => $recipient
        ];
        
        /* Template variables Detail */
        $emailTempVariables = (array(
            'name' => $hostName,
            'order_id' => $hostOrder->getOrderId()
        ));
        /* call send mail method from helper or where you define it */
        $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId);
        
    }
}