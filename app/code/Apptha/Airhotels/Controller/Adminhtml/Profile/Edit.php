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
namespace Apptha\Airhotels\Controller\Adminhtml\Profile;

use Apptha\Airhotels\Controller\Adminhtml\Profile;
/**
 * Clas contains profile edir profile form functions
**/
class Edit extends Profile {
    /**
     * Execute the customer profile edit section to execute
     * 
     **/
    public function execute() {
        $sellerId = $this->getRequest ()->getParam ( 'id' );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $model = $objectManager->get (  'Apptha\Airhotels\Model\Customerprofile' );
        if ($sellerId) {
            $model->load ( $sellerId ,'customer_id');
            if (! $model->getId ()) {
                $this->messageManager->addError ( __ ( 'This Customer no longer exists.' ) );
                $this->_redirect ( '*/*/' );
                return;
            }
        }
        $data = $this->_session->getNewsData ( true );
        if (! empty ( $data )) {
            $model->setData ( $data );
        }
        $this->_coreRegistry->register ( 'airhotels_customerprofile', $model );
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create ();
        $resultPage->setActiveMenu ( 'Apptha_Airhotels::main_menu' );
        $resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Edit host profile data' ) );
        return $resultPage;
    }
}