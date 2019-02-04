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

class Delete extends \Magento\Framework\App\Action\Action {

    /**
     *
     * @var CustomOptionFactory
     */
    protected $registry;
    protected $productRepository;

    /**
     * Constructor
     * \Magento\Framework\Registry $registry
     * \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Catalog\Model\ProductRepository $productRepository, \Magento\Framework\Registry $registry) {
        $this->productRepository = $productRepository;
        $this->registry = $registry;
        parent::__construct ( $context );
    }

    /**
     * Flush cache storage
     */
    public function execute() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        $productId = $this->getRequest ()->getParam ( 'id' );
        $product = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );

        if ($product->getUserId () === $customerSession->getCustomer ()->getId ()) {
            $admin = $objectManager->get ( 'Apptha\Airhotels\Helper\Data' );
            /**
             * Assign admin details
             */
            $adminName = $admin->getAdminName ();
            $adminEmail = $admin->getAdminEmail ();
             /* Sender Detail  */
                $senderInfo = [
                    'name' => $adminName,
                    'email' => $adminEmail,
                ];

             $propertyName = $product->getName ();
            $userId = $product->getUserId ();
            $customer = $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $userId );
            /**
             * Property Email Owner
             */
            $recipient = $customer->getEmail ();
            /**
             * Property Email Owner
             */
            $customerName = $customer->getName ();
            $templateId = 'airhotels_custom_email_propertydelete_template';

            /* Receiver Detail  */
            $receiverInfo = [
                'name' =>$customerName,
                'email' =>$recipient
            ];
            
            $emailTempVariables = (array (
                'ownername' => $adminName,
                'pname' => $propertyName,
                'cname' => $customerName
            ));
            /* We write send mail function in helper because if we want to
             use same in other action then we can call it directly from helper */
            
            /* call send mail method from helper or where you define it*/
            $objectManager->get('Apptha\Airhotels\Helper\Email')->yourCustomMailSendMethod(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,$templateId
                ); 
            $this->registry->register ( 'isSecureArea', true );
            $this->productRepository->delete ( $product );
            $this->messageManager->addSuccess ( __ ( 'The listing has been deleted successfully.' ) );
            $this->_redirect ( 'airhotels/listing/manage/' );
            return;
        } else {
            $this->messageManager->addError ( __ ( 'Access Denied.' ) );
            $this->_redirect ( 'airhotels/listing/manage/' );
            return;
        }
    }
}
