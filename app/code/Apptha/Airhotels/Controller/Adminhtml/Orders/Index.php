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
namespace Apptha\Airhotels\Controller\Adminhtml\Orders;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action {
    /**
     *
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     *
     * @param Context $context
     * @param PageFactory $_resultPageFactory
     */
    public function __construct(Context $context, PageFactory $_resultPageFactory) {
        parent::__construct ( $context );
        $this->resultPageFactory = $_resultPageFactory;
    }

    /**
     * Index action for Orders
     *
     * @return void
     */
    public function execute() {
         
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create ();
        $resultPage->setActiveMenu ( 'Apptha_Airhotels::host_orders' );
        $resultPage->addBreadcrumb ( __ ( 'Manage Airhotels Grid View' ), __ ( 'Manage Booking' ) );
        $resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Manage Booking' ) );

        return $resultPage;
    }
}