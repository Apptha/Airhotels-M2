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
class Status extends \Magento\Framework\App\Action\Action
{

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
 public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
    \Magento\Framework\Registry $registry) {
        $this->productRepository = $productRepository;
        $this->registry = $registry;
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
        /**
         * Getting the status
         */
        $status = $this->getRequest ()->getParam ( 'status' );
        /**
         * product ID
         */
        $productId = $this->getRequest ()->getParam ( 'productid' );
        $product = $this->productRepository->getById($productId);

        if($product->getUserId() === $customerSession->getCustomer()->getId()){
            $this->registry->register('isSecureArea', true);

            if($status == 1){
            $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            } else {
            $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
            }
            $this->productRepository->save($product);



        } else {
            $this->messageManager->addError ( __ ( 'Access Denied.' ) );
            $this->_redirect ( 'airhotels/listing/manage' );
            return;
        }




    }


}
