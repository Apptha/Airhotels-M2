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

class Deletemessage extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $request;
    protected $_contactHost;
    protected $customerSession;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Apptha\Airhotels\Model\ResourceModel\Contacthost\CollectionFactory $_contactHost
     * @param array $context
     */
     public function __construct(Context $context, \Magento\Framework\App\Request\Http $request, 
                                 PageFactory $resultPageFactory,
                                 \Magento\Customer\Model\Session $customerSession,
                                 \Apptha\Airhotels\Model\ResourceModel\Contacthost\CollectionFactory $_contactHost) {
          $this->request = $request;
          $this->_resultPageFactory = $resultPageFactory;
          $this->_contactHost = $_contactHost;
          $this->customerSession = $customerSession;
          $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          parent::__construct ( $context );
     }
    /**
      * Change the delete status to 0 to 1 
      * @return \Magento\Framework\View\Result\Page
      */
    public function execute(){
        /**
           * Getting customer session
           */
       $customerSession = $this->objectManager->get ( 'Magento\Customer\Model\Session' );
          
       /**
        * Checking whether customer is loggedin
        */
        if ($customerSession->isLoggedIn ()) {
        $customerId = $this->customerSession->getId();
        $deleteId = $this->getRequest ()->getParam ('delete_id');
        $id = $this->getRequest ()->getParam ('id');
        $collection = $this->_contactHost->create ();
        //If id 1 notice inbox.phtml 
        if($id == 1){
            $collection->addFieldToFilter ( 'receiver_id' , $customerId )->addFieldToFilter ( 'id', array( 'in' => $deleteId ));
            foreach($collection as $collection){
                $collection->setIsReceiverDelete(1);
                $collection->save();
            }
        } else { 
            $collection->addFieldToFilter ( 'sender_id' , $customerId )->addFieldToFilter ( 'id', array( 'in' => $deleteId ));
            foreach($collection as $collection){
            $collection->setIsSenderDelete(1);
            $collection->save();
        }
        }
         $this->messageManager->addSuccess(__('You have deleted the message(s).'));
        } else {
               /**
                * Redirect to login page
                */
               $this->_redirect ( 'customer/account/login' );
        }
    }
}