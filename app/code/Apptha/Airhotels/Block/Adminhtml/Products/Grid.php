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
namespace Apptha\Airhotels\Block\Adminhtml\Products;

/**
 * Class For Manage Products Grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {
    /**
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    /**
     *
     * @var \Apptha\Grid\Model\GridFactory
     */
    protected $_gridFactory;
    /**
     *
     * @var \Apptha\Grid\Model\Status
     */
    protected $_status;
    /**
     * Initialize constructor
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @return void
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Catalog\Model\ProductFactory $gridFactory, \Apptha\Airhotels\Model\System\Config\Status $status, \Magento\Framework\Module\Manager $moduleManager, array $data = []) {
        $this->_gridFactory = $gridFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct ( $context, $backendHelper, $data );
    }
    /**
     * Constructor function
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $this->setId ( 'productsGrid' );
        $this->setDefaultSort ( 'entity_id' );
        $this->setDefaultDir ( 'DESC' );
        $this->setSaveParametersInSession ( true );
        $this->setUseAjax ( true );
        $this->setVarNameFilter ( 'grid_record' );
    }
    /**
     * Prepare Collection
     *
     * @return $this
     */
    protected function _prepareCollection() {
        $collection = $this->_gridFactory->create ()->getCollection ();
        $collection->addAttributeToSelect ( '*' );
        $collection->addAttributeToFilter ( 'user_id', array (
                'notnull' => true
        ) );

        $this->setCollection ( $collection );
        parent::_prepareCollection ();
        return $this;
    }
    /**
     * Function for Mass Action
     *
     * @return object
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField ( 'entity_id' );
        $this->getMassactionBlock ()->setFormFieldName ( 'id' );
        $this->getMassactionBlock ()->addItem ( 'Approve', [
                'label' => __ ( 'Approve' ),
                'url' => $this->getUrl ( 'airhotelsadmin/products/massapprove' )
        ] );
        $this->getMassactionBlock ()->addItem ( 'Disapprove', [
                'label' => __ ( 'Disapprove' ),
                'url' => $this->getUrl ( 'airhotelsadmin/products/massdisapprove' )
        ] );
        $this->getMassactionBlock ()->addItem ( 'Enable', [
                'label' => __ ( 'Enable' ),
                'url' => $this->getUrl ( 'airhotelsadmin/products/massenable' )
        ] );
        $this->getMassactionBlock ()->addItem ( 'Disable', [
                'label' => __ ( 'Disable' ),
                'url' => $this->getUrl ( 'airhotelsadmin/products/massdisable' )
        ] );
        return $this;
    }
    /**
     *Function to prepare columns
     * @return object
     */
    protected function _prepareColumns() {
        $this->addColumn ( 'entity_id', [
                'header' => __ ( 'ID' ),
                'type' => 'number',
                'index' => 'entity_id'
        ] );
        $this->addColumn ( 'name', [
                'header' => __ ( 'Name' ),
                'type' => 'text',
                'index' => 'name'
        ] );
        $this->addColumn ( 'sku', [
                'header' => __ ( 'Sku' ),
                'type' => 'text',
                'index' => 'sku'
        ] );
        $this->addColumn ( 'price', [
                'header' => __ ( 'Price' ),
                'type' => 'text',
                'index' => 'price'
        ] );

        $this->addColumn ( 'booking_type', [
                'header' => __ ( 'Booking Type' ),
                'type' => 'text',
                'index' => 'entity_id',
                'renderer' => '\Apptha\Airhotels\Block\Adminhtml\Products\Grid\Renderer\Bookingtype',
                'filter_condition_callback' => array($this, 'bookingType')
        ] );


        $this->addColumn ( 'product_status', array (
                'header' => __ ( 'Status' ),
                'index' => 'status',
                'type' => 'options',
                'options'=> $this->_status->toStatusArray()
        ) );
        $this->addColumn ( 'product_approval', array (
                'header' => __ ( 'Approval Status' ),
                'index' => 'property_approved',
                'type' => 'options',
                'options'=> $this->_status->toOptionArray()
        ) );
        $this->addColumn ( 'user_id', [
                'header' => __ ( 'Host Name' ),
                'type' => 'text',
                'index' => 'user_id',
                'renderer' => '\Apptha\Airhotels\Block\Adminhtml\Host\Grid\Renderer\Name',
                'filter_condition_callback' => array($this, 'hostName')
        ] );
        $this->addColumn ( 'email', array (
                'header' => __ ( 'Host Email' ),
                'index' => 'user_id',
                'type' => 'email',
                'renderer' => '\Apptha\Airhotels\Block\Adminhtml\Products\Grid\Renderer\HostEmail',
                'filter_condition_callback' => array($this, 'hostEmail')
        ) );

        $this->addColumn ( 'action', array (
                'header' => __ ( 'Action' ),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array (
                        array (
                                'caption' => __ ( 'Edit' ),
                                'url' => array (
                                        'base' => 'catalog/product/edit',
                                        'params' => array (
                                                'store' => $this->getRequest ()->getParam ( 'store' )
                                        )
                                ),
                                'field' => 'id'
                        )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores'
        ) );



        $block = $this->getLayout ()->getBlock ( 'grid.bottom.links' );
        if ($block) {
            $this->setChild ( 'grid.bottom.links', $block );
        }

        return parent::_prepareColumns ();
    }
    /**
     *Function to filter collection based on host email
     * @return object
     */
    protected function hostEmail($collection, $column)
    {
        $userId = array();
        $email = $column->getFilter()->getValue();
        $customerCollection = $this->objectManager->get('Magento\Customer\Model\Customer')->getCollection()->addAttributeToFilter('email', array('eq' => $email));

        foreach($customerCollection as $customer){
          $userId[] =  $customer->getId();
        }

        if (!$email) {
            return $this;
        }
        $this->getCollection()->addFieldToFilter('user_id', array('eq' => $userId));

        return $this;
    }

    /**
     *Function to filter collection based on booking type
     * @return object
     */

    protected function bookingType($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $attributeFactory = $this->objectManager->get('\Magento\Catalog\Model\Product\Attribute\Repository')->get('booking_type')->getOptions();
        $bookingTypeId='';
        foreach($attributeFactory as $attributeValue){
            if($attributeValue->getLabel() == $value ){
                $bookingTypeId = $attributeValue->getValue();
            }
        }

        if (!$value) {
            return $this;
        }
        $this->getCollection()->addFieldToFilter('booking_type', array('eq' => $bookingTypeId));

        return $this;
    }
    /**
     *Function to filter collection based on host name
     * @return object
     */
    protected function hostName($collection, $column)
    {
        $userId = array();
        $name = $column->getFilter()->getValue();
        $customerCollection = $this->objectManager->get('Magento\Customer\Model\Customer')->getCollection() ->addExpressionAttributeToSelect('fullname', 'CONCAT({{firstname}}, " ", {{lastname}})',array('firstname','lastname'));
        $customerCollection->addFieldToFilter('fullname', array('eq' => $name));
        foreach($customerCollection as $customer){
              $userId[] =  $customer->getId();
        }
        if (!$name) {
            return $this;
        }
        $this->getCollection()->addFieldToFilter('user_id', array('in' => $userId));

        return $this;
    }
}
