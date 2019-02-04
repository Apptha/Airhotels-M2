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
use Magento\Framework\View\Result\PageFactory ;
use Magento\Customer\Model\Session;

class Hostinfo extends \Magento\Framework\App\Action\Action{
   protected $_resultPageFactory;

   /**
    * @param Context $context
    * @param PageFactory $resultPageFactory
    */
    public function __construct(Context $context, PageFactory $resultPageFactory){
        $this->_resultPageFactory = $resultPageFactory;
          $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        parent::__construct($context);
    }

     /**
      * Load the layout and view layout
      */
   public function execute() {
       $this->_view->loadLayout ();
       $this->_view->renderLayout ();
    }
}