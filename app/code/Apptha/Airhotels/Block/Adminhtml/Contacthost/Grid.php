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
namespace Apptha\Airhotels\Block\Adminhtml\Contacthost;

/**
 * Class For Manage Contact Host Grid
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
    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Apptha\Airhotels\Model\Contacthost $gridFactory, \Apptha\Airhotels\Model\System\Config\Status $status, \Magento\Framework\Module\Manager $moduleManager, array $data = []) {
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
        $this->setId ( 'customersGrid' );
        $this->setDefaultSort ( 'id' );
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
        $collection->getSelect()->group('product_id');
        $this->setCollection ( $collection );
        parent::_prepareCollection ();
        return $this;
    }
    
    /**
     *Function to prepare columns
     * @return object
     */
    protected function _prepareColumns() {              
        
        $this->addColumn ( 'receiver_id', [
                'header' => __ ( 'Host Name' ),
                'type' => 'number',
                'renderer' => '\Apptha\Airhotels\Block\Adminhtml\Contacthost\Grid\Renderer\Hostname',                
                'index' => 'receiver_id',
                'filter' => false,
                'sortable' => false
                
        ] );
          $this->addColumn ( 'product_id', [
                'header' => __ ( 'Listing Name' ),
                'type' => 'number',
                'renderer' => '\Apptha\Airhotels\Block\Adminhtml\Contacthost\Grid\Renderer\Productname',                
                'index' => 'product_id',
                 'filter' => false,
                 'sortable' => false
                
        ] );

        $this->addColumn ( 'action', array (
                'header' => __ ( 'Action' ),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getProductId',
                'actions' => array (
                        array (
                                'caption' => __ ( 'View' ),
                                'url' => array (
                                        'base' => 'airhotelsadmin/*/edit/id/',
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
}
