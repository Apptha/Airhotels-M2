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
namespace Apptha\Airhotels\Controller\Adminhtml\Contacthost;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action {
    /**
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

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
     *
     * @return void
     */
    public function execute() {

        $resultPage= $this->resultPageFactory->create ();
        /**
         * To set active menu
         */
        $resultPage->setActiveMenu ( 'Apptha_Airhotels::airhotels_contacthost' );
        /**
         * Setting title for host payments
         */
        $resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Contact Host Messages' ) );
        /**
         * Return result page
         */ 
        return $resultPage;
    }
}