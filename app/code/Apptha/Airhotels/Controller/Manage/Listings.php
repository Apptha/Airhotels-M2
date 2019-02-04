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
namespace Apptha\Airhotels\Controller\Manage;

class Listings extends \Magento\Framework\App\Action\Action {
    protected $dataHelper;
    public function __construct(\Magento\Framework\App\Action\Context $context) {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        parent::__construct ( $context );
    }

    /**
     * Function to load all listing for a particular host
     *
     * @return $array
     */
    public function execute() {
        /**
         * Getting customer session
         */
        $customer = $this->objectManager->get ( 'Magento\Customer\Model\Session' );

            /**
             * Checking whether customer is loggedin
             */
            if ($customer->isLoggedIn ()) {
                /**
                 * Load layout for manage listings
                 */
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            }  else {

                /**
                 * Redirect to login page
                 */
                $this->_redirect ( 'customer/account/login' );
            }

    }
}