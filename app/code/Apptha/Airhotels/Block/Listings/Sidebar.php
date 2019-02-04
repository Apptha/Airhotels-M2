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
class Sidebar extends \Magento\Framework\View\Element\Template {

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
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->stockRegistry = $stockRegistry;
        $this->messageManager = $messageManager;
        $this->_currency = $currency;
        parent::__construct ( $context, $data );
    }




}