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
 /**
 * Clas contains inbox Message functions
**/
namespace Apptha\Airhotels\Controller\Message; 

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Apptha\Airhotels\Model\CustomerreplyFactory;

class Replymessage extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $customerSession; 
    protected $_customers;
    protected $inboxCollection;
    protected $_productRepository;
     /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Customer $customers
     * @param CustomerreplyFactory $_customerreply
     * @param array $context
     */
     public function __construct(Context $context, PageFactory $resultPageFactory, 
                                 \Apptha\Airhotels\Model\ResourceModel\Contacthost\CollectionFactory $inboxCollection,
                                 \Magento\Customer\Model\Session $customerSession,\Magento\Customer\Model\Customer $customers,
                                 \Magento\Catalog\Model\ProductFactory $productRepository,
                                 CustomerreplyFactory $_customerreply) {
          $this->_resultPageFactory = $resultPageFactory;
          $this->customerSession = $customerSession;
          $this->_inboxCollection = $inboxCollection;
          $this->_customerreply = $_customerreply;
          $this->_productloader = $productRepository;
          $this->_customers = $customers;
          $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          parent::__construct ( $context );
     }
    /**
     * Create reply message.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute(){
          if ($this->customerSession->isLoggedIn ()) {
              $senderId = $this->customerSession->getCustomer()->getId();
              $replyMessage = $this->getRequest ()->getParam ('reply_desc');
              $messageId = $this->getRequest ()->getParam ('message_id');
              $receiverId = $this->getRequest ()->getParam ('receiver_id');
              
              $customerreply = $this->_customerreply->create();
              $customerreply->setSenderId ( $senderId );
              $customerreply->setMessageId ( $messageId );
              $customerreply->setMessage ( $replyMessage );
              $customerreply->setReceiverId ( $receiverId );
              $customerreply->save();
              $inboxCollection = $this->_inboxCollection->create ()->addFieldToFilter ( 'id', $messageId );
              $productId = $inboxCollection->getFirstItem()->getProductId();
              $inbox = $inboxCollection->addFieldToFilter ( 'receiver_id', $senderId )
                  ->addFieldToFilter ( 'reply_message_id', 0 );
              foreach($inbox as $inbox) {
                    $contactHostCustomerId = $inbox->getSenderId();
                    if($senderId != $contactHostCustomerId){
                        $inbox->setReplyMessageId ( $receiverId );
                        $inbox->setSentMessageId ( $senderId );
                        $inbox->save();
                    } 
              }
                //loading host details
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
                //Sender Details
                $customerSession = $this->objectManager->get('Magento\Customer\Model\Session');
                //Get product Name
                $product = $this->_productloader->create()->load($productId);
                $productName = $product->getName();
                /**
                 * Property Email Owner
                 */
                $recipient = $customerSession->getCustomer()->getEmail();
                $hostName = $customerSession->getCustomer()->getName();
                //Receiver Details
                $receiver = $this->getReceiverDet($receiverId);
                $receiverName = $receiver->getName();
                $receiverEmail = $receiver->getEmail();
                
                $admin = $objectManager->get('Apptha\Airhotels\Helper\Data');
                /**
                 * Assign admin details
                 */
                $adminName = $admin->getAdminName();
                $adminEmail = $admin->getAdminEmail();
                $templateId = 'airhotels_contact_host_reply';
                /* Sender Detail */
                $senderInfo = [
                    'name' => $hostName,
                    'email' => $recipient
                ];
                /* Template variables Detail */
                $emailTempVariables = (array(
                    'receiver_name' => $receiverName,
                    'message' => $replyMessage,
                    'product_name' => $productName
                ));
                /* Receiver Detail */
                $receiverInfo = [
                    'name' => $adminName,
                    'email' => $adminEmail
                ];
                
                /* call send mail method from helper or where you define it */
                $objectManager->get('Apptha\Airhotels\Helper\Email')->contacthostMailSendMethod($emailTempVariables, $senderInfo, $receiverInfo, $templateId, $receiverEmail);
                $this->messageManager->addSuccess(__('Your message has been sent successfully.'));
                
                $this->_redirect ( 'booking/message/inbox' );
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
    public function getReceiverDet($receiverId) {
        return $this->_customers->load($receiverId);
    }
}