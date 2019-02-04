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

use Apptha\Airhotels\Controller\Adminhtml\Payout;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Clas contains profile edir profile form functions
**/
class Edit extends Payout  {
     
    /**
     * Execute the customer profile edit section to execute
     * 
     **/
    public function execute() {
        $getId = $this->getRequest ()->getParam ( 'id' );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $model = $objectManager->get (  'Apptha\Airhotels\Model\Hostorder' );
        if ($getId) {
            $model->load ( $getId ,'id');
            if (! $model->getId ()) {
                $this->messageManager->addError ( __ ( 'This order no longer exists.' ) );
                $this->_redirect ( 'airhotelsadmin/orders/index/' );
                return;
            }
        }
        $this->_coreRegistry->register ( 'airhotels_payoutedit', $model );
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create ();
        $resultPage->setActiveMenu ( 'Apptha_Airhotels::main_menu' );
        $resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Update Transaction' ) );
        return $resultPage;
    }
}