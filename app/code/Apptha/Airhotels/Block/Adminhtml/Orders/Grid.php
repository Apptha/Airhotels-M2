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
namespace Apptha\Airhotels\Block\Adminhtml\Orders;

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
    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Apptha\Airhotels\Model\Hostorder $gridFactory, \Apptha\Airhotels\Model\System\Config\Status $status, \Magento\Framework\Module\Manager $moduleManager, array $data = []) {
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
        $this->setId ( 'ordersGrid' );
        $this->setDefaultSort ( 'order_id' );
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
        $collection = $this->_gridFactory->getCollection ();
        $collection->addFieldToSelect ( '*' );
        $collection->addFieldToFilter ( 'order_status','complete' );
        $collection->addFieldToFilter ( 'host_id', array (
                'notnull' => true
        ) );
        $collection->setOrder('id','DESC');
        
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
        $this->setMassactionIdField ( 'id' );
        return $this;
    }
    /**
     *Function to prepare columns
     * @return object
     */
    protected function _prepareColumns() {
        $this->addColumn ( 'id', [
                'header' => __ ( 'ID' ),
                'type' => 'number',
                  'filter' => false,
                'index' => 'id'
        ] );
        $this->addColumn ( 'order_item_id', [
                'header' => __ ( 'Order ID' ),
                'type' => 'text',
                  'sortable' => false,
                'index' => 'order_id'
        ] );
        $this->addColumn ( 'listing_name', [
                'header' => __ ( 'Listing Name' ),
                  'sortable' => false,
                'type' => 'text',
                'index' => 'listing_name'
        ] );
        $this->addColumn ( 'fromdate', [
                'header' => __ ( 'From' ),
                'type' => 'date',
                  'filter' => false,
                  'sortable' => false,
                'index' => 'fromdate'
        ] );
        $this->addColumn ( 'todate', [
                'header' => __ ( 'To' ),
                'type' => 'date',
                  'filter' => false,
                  'sortable' => false,
                'index' => 'todate'
        ] );

        $this->addColumn ( 'host_product_total', [
                'header' => __ ( 'Order Total' ),
                'type' => 'text',
                'filter' => false,
                  'sortable' => false,
                'index' => 'host_product_total'
        ] );
        $this->addColumn ( 'host_amount', [
                'header' => __ ( 'Host Amount' ),
                'type' => 'text',
                'filter' => false,
                  'sortable' => false,
                'index' => 'host_amount'
        ] );


        $this->addColumn ( 'commission_fee', array (
                'header' => __ ( 'Commission Fee' ),
                'index' => 'commission_fee',
                'filter' => false,
                  'sortable' => false,
                'type' => 'text'
        ) );
        $this->addColumn ( 'service_fee', array (
                'header' => __ ( 'Service Fee' ),
                'index' => 'service_fee',
                'filter' => false,
                  'sortable' => false,
                'type' => 'text'
        ) );
        $this->addColumn ( 'order_status', [
                'header' => __ ( 'Status' ),
                'type' => 'text',
                  'sortable' => false,
                'index' => 'order_status'
        ] );
        $this->addColumn ( 'action', array (
                'header' => __ ( 'Action' ),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getId',
                'renderer' => '\Apptha\Airhotels\Block\Adminhtml\Orders\Grid\Renderer\Paymentaction',
                'filter' => false,
                'sortable' => false,
                'index' => 'id'
        ) );
        $this->addColumn ( 'view_order', array (
                'header' => __ ( 'View Order' ),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getOrderId',
                'actions' => array (
                        array (
                                'caption' => __ ( 'View' ),
                                'url' => array (
                                        'base' => 'sales/order/view',
                                        'params' => array (
                                                'store' => $this->getRequest ()->getParam ( 'store' )
                                        )
                                ),
                                'field' => 'order_id'
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
}