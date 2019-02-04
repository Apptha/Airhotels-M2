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
namespace Apptha\Airhotels\Controller\Adminhtml\Products;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Apptha\Airhotels\Controller\Adminhtml\Hosts;

class Massdisapprove extends \Magento\Backend\App\Action {
    /**
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory) {
        parent::__construct ( $context );
        $this->resultPageFactory = $resultPageFactory;
    }
    /**
     * Index action
     *
     * @return void
     */
    public function execute() {
        $result = $this->getRequest ()->getParam ( 'id' );
        foreach ( $result as $approvalProductId ) {
            try {
                $customerFactory = $this->_objectManager->create ( '\Magento\Catalog\Model\Product' );
                $customerFactory->load ( $approvalProductId )->setStatus ( 2 )->setPropertyApproved ( 0 )->save ();


            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
}