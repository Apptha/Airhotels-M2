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
namespace Apptha\Airhotels\Block\Listings;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\StockRegistry;
use Zend\Form\Annotation\Object;

/**
 * This class used to display the products collection
 */
class Manage extends \Magento\Framework\View\Element\Template {

    /**
     * Initilize variable for product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    protected $_currency;
    /**
     * Initilize variable for stock registry
     *
     * @var Magento\CatalogInventory\Model\StockRegistry
     */
    protected $stockRegistry;
    protected $messageManager;

    /**
     *
     * @param Template\Context $context
     * @param ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(Template\Context $context, Collection $productFactory, \Magento\Directory\Model\Currency $currency, StockRegistry $stockRegistry, \Magento\Catalog\Model\Product $product, \Magento\Framework\Message\ManagerInterface $messageManager, array $data = []) {
        $this->productFactory = $productFactory;
        $this->product = $product;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->stockRegistry = $stockRegistry;
        $this->messageManager = $messageManager;
        $this->_currency = $currency;
        parent::__construct ( $context, $data );
    }

    /**
     * Set product collection uisng ProductFactory object
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $collection = $this->getFilterProducts ();
        $this->setCollection ( $collection );
    }
    /**
     * Getting website current date
     */
    public function currentDate(){
    $objDate = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
    return $objDate->gmtDate();
    }

    /**
     * Getting website base url
     */
    public function getBaseUrl()
    {
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');

        return $storeManager->getStore()->getBaseUrl();
    }

    /**
     * Prepare layout for manage product
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        parent::_prepareLayout ();
        /**
         *
         * @var \Magento\Theme\Block\Html\Pager
         */
        $pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'airhotels.product.list.pager' );
        $pager->setAvailableLimit(array(
                10 => 10,
                20 => 20,
                50 => 50
            ))->setShowAmounts ( false )->setCollection ($this->getCollection ());
        $this->setChild ( 'pager', $pager );
        $this->getCollection () ->load();
        return $this;
    }

    /**
     * Get Manage product pager html
     *
     * @return string
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }

    /**
     * Product filter
     *
     * @return Object
     */
    public function getFilterProducts() {

        $customerSession = $this->_objectManager->create ( 'Magento\Customer\Model\Session' );

        /**
         * Filter action
         */
        $productIds = array ();
        $delete = $this->getRequest ()->getPost ( 'multi' );
        $productIds = $this->getRequest ()->getParam ( 'id' );
        if (count ( $productIds ) > 0 && $delete == 'delete') {
            $deleteFlag = 0;
            foreach ( $productIds as $productId ) {
                $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
                $customerId = $customerSession->getCustomer()->getId ();
                $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
                $productHostId = $product->getUserId ();
                if ($customerId == $productHostId) {
                    $this->_objectManager->get ( 'Magento\Framework\Registry' )->register ( 'isSecureArea', true );
                    $product->delete ();
                    $this->_objectManager->get ( 'Magento\Framework\Registry' )->unregister ( 'isSecureArea' );
                    $deleteFlag = 1;
                }
            }
            if ($deleteFlag == 1) {
                $this->messageManager->addSuccess ( __ ( 'The product has been deleted successfully.' ) );
            }
        }
        /**
         * Filter by product attributes
         */

        $product = $this->productFactory->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'user_id', $customerSession->getCustomer()->getId () );
        $product->addAttributeToFilter ( 'visibility', array (
                'eq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
        ) );
        $product->addAttributeToSort ( 'entity_id', 'DESC' );

        /**
         * Return product object
         */
        return $product;
    }


    /**
     * Get media image url
     *
     * @return string $mediaImageUrl
     */
    public function getMediaImageUrl() {
        return $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'catalog/product';
    }
}
