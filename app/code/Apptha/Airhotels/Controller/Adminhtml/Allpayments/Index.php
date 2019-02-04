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
namespace Apptha\Airhotels\Controller\Adminhtml\Allpayments;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action {
    /**
     *
     * @var PageFactory
     */
    protected $viewresult;

    /**
     *
     * @param Context $context
     * @param PageFactory $viewresult
     */
    public function __construct(Context $context, PageFactory $viewresult) {
        parent::__construct ( $context );
        $this->viewresult = $viewresult;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute() {

        $viewResult = $this->viewresult->create ();
        /**
         * To activate marektplace menu
         */
        $viewResult->setActiveMenu ( 'Apptha_Airhotels::manage_payments' );

        /**
         * Add breadcrumb for subscribed profiles
         */
        $viewResult->addBreadcrumb ( __ ( 'Host Payments' ), __ ( 'All Payments' ) );

        $hostId = $this->getRequest ()->getParam ( 'id' );
        if (empty ( $hostId )) {
            /**
             * Setting title for subscripbed profiles
             */
            $viewResult->getConfig ()->getTitle ()->prepend ( __ ( 'All Payments' ) );
        } else {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $storeName = $objectManager->get ( 'Apptha\Airhotels\Model\Customerprofile' )->load ( $hostId, 'customer_id' )->getStoreName ();
            if (empty ( $storeName )) {
                $storeName = $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $hostId )->getFirstname ();
            }
            $viewResult->getConfig ()->getTitle ()->prepend ( __ ( 'All Payments for ' ) . '"' . $storeName . '"' );
        }

        /**
         * Return page
         */
        return $viewResult;
    }
}
