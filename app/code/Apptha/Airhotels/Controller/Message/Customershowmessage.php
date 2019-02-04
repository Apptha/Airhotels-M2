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

class Customershowmessage extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Magento\Framework\App\ObjectManager::getInstance ()
     * @param PageFactory $resultPageFactory
     * @param array $context
     */
     public function __construct(Context $context, PageFactory $resultPageFactory) {
          $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          $this->_resultPageFactory = $resultPageFactory;
          parent::__construct ( $context );
     }
    /**
     * Create show message.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute(){
      ob_start();
        /**
           * Getting customer session
           */
          $customerSession = $this->objectManager->get ( 'Magento\Customer\Model\Session' );
          
          /**
           * Checking whether customer is loggedin or not and load manage listing
           */
          if ($customerSession->isLoggedIn ()) {
               
              echo $this->_view->getLayout()->createBlock ("Apptha\Airhotels\Block\Message\Customershowmessage" )->setTemplate ( "airhotels/message/customer_showmessage.phtml" )->toHtml();
          } else {
               
               /**
                * Redirect to login page
                */
               $this->_redirect ( 'customer/account/login' );
          }
    }
}