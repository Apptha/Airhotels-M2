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
namespace Apptha\Airhotels\Controller\Profile;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
class Accountverification extends \Magento\Framework\App\Action\Action{
              /**
               * Get current customer details
               * @var object
               */
              protected $customerAccountSession;
       /**
        * Load the page collection
        * @var object
        */
       protected $_resultPageFactory;
      /**
        * __construct to load the model collection
        * @param Context $context
        * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
        * @param Session $customerSession
        */
       public function __construct(Context $context,\Magento\Framework\View\Result\PageFactory $resultPageFactory, Session $customerSession){
                    $this->customerAccountSession = $customerSession;
                    $this->_resultPageFactory = $resultPageFactory;
              parent::__construct($context);
       }
       /**
        * To load the edit account verification layour and phtml
        * {@inheritDoc}
        * @see \Magento\Framework\App\ActionInterface::execute()
        */
       public function execute(){
          if ($this->customerAccountSession->isLoggedIn ()){
                     $resultPage = $this->_resultPageFactory->create();
          }else{
             /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
             $resultPage = $this->resultRedirectFactory->create ();
             $this->messageManager->addNotice(__("Login Reuqire For Edit Profile. So Please <i class='fa fa-lock'></i> Login Now And Edit Your Post."));
             $resultPage->setPath ( 'customer/account/login' );
          }
          return $resultPage;
       }
}