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
 * Clas contains Sent message functions
**/
namespace Apptha\Airhotels\Controller\Message; 
use Magento\Framework\App\Action\Context; 
class Sent extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param array $context
     */
    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory){
        $this->_resultPageFactory = $resultPageFactory;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        parent::__construct($context);
    }
    /**
     * Create sent items.
     *
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
               /**
                * Load layout for manage listings
                */
               $this->_view->loadLayout ();
               $this->_view->renderLayout ();
          } else {
               
               /**
                * Redirect to login page
                */
               $this->_redirect ( 'customer/account/login' );
          }
    }
}