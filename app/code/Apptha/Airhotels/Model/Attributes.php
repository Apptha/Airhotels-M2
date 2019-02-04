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
namespace Apptha\Airhotels\Model;

/**
 * Airhotels attributes model
 */
class Attributes extends \Magento\Framework\DataObject{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Framework\App\Config\ValueInterface
     */
    protected $_backendModel;
    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;
    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_configValueFactory;
    /**
     * @var int $_storeId
     */
    protected $_storeId;
    /**
     * @var string $_storeCode
     */
    protected $_storeCode;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
     * @param \Magento\Framework\App\Config\ValueInterface $backendModel,
     * @param \Magento\Framework\DB\Transaction $transaction,
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory,
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\ValueInterface $backendModel,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $entityAttributeCollection,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResult,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_backendModel = $backendModel;
        $this->_transaction = $transaction;
        $this->_resource = $resource;
        $this->jsonResult = $jsonResult;
        $this->checkoutSession = $checkoutSession;
        $this->eavAttributeCollection = $entityAttributeCollection;

        $this->_configValueFactory = $configValueFactory;
        $this->_storeId=(int)$this->_storeManager->getStore()->getId();
        $this->_storeCode=$this->_storeManager->getStore()->getCode();
        $this->product = $product;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }


    /**
     * Function Name: 'getcancelpolicy'
     * Retrieve attribute id for cancel policy
     *
     * @return integer
     */
    public function getcancelpolicy() {
        /**
         * Getting entity attribute model
         */
        return $this->eavAttributeCollection->getIdByCode ( 'catalog_product', 'cancelpolicy' );
    }
    /**
     * Function Name: 'getmediagallery'
     * Retrieve attribute id for media gallery
     *
     * @return integer
     */
    public function getmediagallery() {
        /**
         * Getting entity attribute model
         */
        return $this->eavAttributeCollection->getIdByCode ( 'catalog_product', 'media_gallery' );
    }
    /**
     * Function Name: 'getprivacy'
     * Retrieve attribute id for privacy
     *
     * @return integer
     */
    public function getprivacy() {
        /**
         * Getting entity attribute model
         */
        return $this->eavAttributeCollection->getIdByCode ( 'catalog_product', 'privacy' );
    }
    /**
     * Function Name: 'getpropertytype'
     * Retrieve attribute id for propertytype
     *
     * @return integer
     */
    public function getpropertytype() {
        /**
         * Getting entity attribute model
         */
        return $this->eavAttributeCollection->getIdByCode ( 'catalog_product', 'booking_type' );
    }

    /**
     * Function Name: 'getBedRoom'
     * Retrieve attribute id for bed_room
     *
     * @return integer
     */
    public function getBedRoom() {
        /**
         * Getting entity attribute model
         */
        return $this->eavAttributeCollection->getIdByCode ( 'catalog_product', 'rooms' );
    }

    /**
     * Function Name: 'getBedType'
     * Retrieve attribute id for bed_type
     *
     * @return integer
     */
    public function getBedType() {
        /**
         * Getting entity attribute model
         */
        return $this->eavAttributeCollection->getIdByCode ( 'catalog_product', 'bed_type' );
    }

    /**
     * Function Name: 'getamenity'
     * Retrieve attribute id for amenity
     *
     * @return integer
     */
    public function getamenity() {
        /**
         * Getting entity attribute model
         */
        return $this->eavAttributeCollection->getIdByCode ( 'catalog_product', 'amenity' );
    }

    /**
     * Function Name: 'getHostLanguages'
     * Retrieve attribute id for host languages
     *
     * @return integer
     */
    public function getHostLanguages() {
        /**
         * Getting entity attribute model
         */
        return $this->eavAttributeCollection->getIdByCode ( 'catalog_product', 'host_languages' );
    }

    /**
     * Function Name: 'getHouseRules'
     * Retrieve attribute id for house rules
     *
     * @return integer
     */
    public function getHouseRules() {
        /**
         * Getting entity attribute model
         */
        return $this->eavAttributeCollection->getIdByCode ( 'catalog_product', 'house_rules' );
    }
}
