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
 * @version     1.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2017 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
/**
 * This class contains host review edit functionality
 */
namespace Apptha\Airhotels\Controller\Adminhtml\Payments;

use Apptha\Airhotels\Controller\Adminhtml\Payments;

class Edit extends Payments {
    /**
     * host payment edit action
     */
    public function execute() {
        /**
         * Gettin plan id from query string
         */
        $profileId = $this->getRequest ()->getParam ( 'id' );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $hostModel = $objectManager->get ( 'Apptha\Airhotels\Model\Customerprofile' );
        /**
         * Checking for host id exist or not
         */
        if ($profileId) {
            $hostModel->load ( $profileId );
            if (! $hostModel->getId ()) {
                $this->messageManager->addError ( __ ( 'This Host no longer exists.' ) );
                $this->_redirect ( '*/*/' );
                return;
            }
        }
        /**
         * Restore previously entered form data from session
         */
        $paymentsData = $this->_session->getNewsData ( true );
        if (! empty ( $paymentsData )) {
            $hostModel->setData ( $paymentsData );
        }
        /**
         * Creaging register for subscription plan model
         */
        $this->_coreRegistry->register ( 'airhotels_payments', $hostModel );
        /** @var \Magento\Backend\Model\View\Result\Page $resultHtml */
        $resultHtml = $this->_resultPageFactory->create ();
        /**
         * Activate airhotels menu
         */
        $resultHtml->setActiveMenu ( 'Apptha_Airhotels::main_menu' );

        $resultHtml->getConfig ()->getTitle ()->prepend ( __ ( 'Pay' ) );
        return $resultHtml;
    }
}
