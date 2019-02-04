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
namespace Apptha\Airhotels\Controller\Adminhtml\Cities;

use Apptha\Airhotels\Controller\Adminhtml\Cities;
/**
 * Clas contains cities edit  form functions
**/
class Edit extends Cities {
    /**
     * Execute the cities edit section to execute
     * 
     **/
    public function execute() {
        $Id = $this->getRequest ()->getParam ( 'id' );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $model = $objectManager->get (  'Apptha\Airhotels\Model\City' );
        if ($Id) {
            $model->load ($Id);
            if (!$model->getId ()) {
                $this->messageManager->addError ( __ ( 'This City no longer exists.' ) );
                $this->_redirect ( '*/*/' );
                return;
            }
        }
        $data = $this->_session->getNewsData ( true );
        if (!empty ( $data )) {
            $model->setData ( $data );
        }
        $this->_coreRegistry->register ( 'airhotels_cities', $model );
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create ();
        $resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Edit Cities' ) );
        $resultPage->setActiveMenu ( 'Apptha_Airhotels::main_menu' );
        return $resultPage;
    }
}