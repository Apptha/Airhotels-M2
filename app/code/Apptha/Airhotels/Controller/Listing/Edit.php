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
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory as CustomOptionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
class Edit extends \Magento\Framework\App\Action\Action
{

/**
     *
     * @var CustomOptionFactory
     */
    protected $resultPageFactory;
    protected $productRepository;
    protected $productFactory;

    protected $customOptionFactory;
    protected $_file;

    /**
     * Constructor
     * \Magento\Framework\View\Result\PageFactory $resultPageFactory,
     * \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * \Magento\Catalog\Model\ProductFactory $productFactory
     * CustomOptionFactory $customOptionFactory
     */
 public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Model\ProductFactory $productFactory, CustomOptionFactory $customOptionFactory,\Magento\Framework\Filesystem\Driver\File $file) {
        $this->resultPageFactory = $resultPageFactory;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;

        $this->customOptionFactory = $customOptionFactory;
        $this->_file = $file;
        parent::__construct ( $context );
    }

    /**
     * Flush cache storage
     *
     */
    public function execute()
    {

        $objectGroupManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectGroupManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerSession->getId ();

        $entityId = ( int ) $this->getRequest ()->getParam ( 'id' );
        /**
         * Load the entity ID Value
         */
        $collectionValue = $objectGroupManager->create ( 'Magento\Catalog\Model\Product' )->load ( $entityId );
        /**
         * Customer ID Value.
         */
        $userId = $collectionValue->getUserId ();
        /**
         * Make sure the '$customerId' and '$Customer_id' are not same.
         */
        if ($customerId != $userId) {
            $this->messageManager->addError ( __( "Access denied" ) );
            $this->_redirect ( 'airhotels/manage/listings/' );
            return;
        }
        if (isset ( $entityId )) {
            $customerSession->setCurrentExperienceId ( $entityId );
            $this->_redirect ( '*/listing/form/step/basic/id/'.$entityId);
            return;
        }

    }


}
