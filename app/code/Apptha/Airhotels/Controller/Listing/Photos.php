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
 * */
namespace Apptha\Airhotels\Controller\Listing;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
class Photos extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;


    /**
     * Constructor
     * \Magento\Framework\View\Result\PageFactory $resultPageFactory,
     * \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * \Magento\Catalog\Model\ProductFactory $productFactory
     */
 public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct ( $context );
    }

    /**
     * Flush cache storage
     *
     */
    public function execute()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerEntityId = $customerSession->getId ();

        $productId = ( int ) $this->getRequest ()->getParam ( 'id' );
        /**
         * Load the entity ID Value
         */
        $productCollection = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
        /**
         * Customer ID Value.
         */
        $CustomerId = $productCollection->getUserId ();
        /**
         * Make sure the '$customerId' and '$customerEntityId' are not same.
         */
        if ($customerEntityId != $CustomerId) {
            $this->messageManager->addError ( __( "Access denied" ) );
            $this->_redirect ( 'airhotels/manage/listings/' );
            return;
        }
        if (isset ( $productId )) {
            $customerSession->setCurrentExperienceId ( $productId );
            $this->_redirect ( '*/listing/form/step/photos' );
            return;
        }



    }


}
