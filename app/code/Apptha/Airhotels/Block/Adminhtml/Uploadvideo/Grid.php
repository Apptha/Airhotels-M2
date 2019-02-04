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
* @version     1.0.0
* @author      Apptha Team <developers@contus.in>
* @copyright   Copyright (c) 2017 Apptha. (http://www.apptha.com)
* @license     http://www.apptha.com/LICENSE.txt
*
*/
namespace Apptha\Airhotels\Block\Adminhtml\Uploadvideo;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;
protected $_collectionFactory;
    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;
    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
\Apptha\Airhotels\Model\ResourceModel\Uploadvideo\Collection $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
$this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
    }
    /**
     * @return Store
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
try{
$collection =$this->_collectionFactory->load();
$this->setCollection($collection);
parent::_prepareCollection();
return $this;
}
catch(Exception $e)
{
echo $e->getMessage();
}
    }
    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()!=null && $column->getId() == 'websites') {
            $this->getCollection()->joinField(
                'websites',
                'catalog_product_website',
                'website_id',
                'product_id=entity_id',
                null,
                'left'
            );
        }
        return parent::_addColumnFilterToCollection($column);
    }
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
$this->addColumn(
            'image_url',
            [
                'header' => __('Image url'),
                'index' => 'image_url',
                'class' => 'image_url',
                'getter' => 'getId',
                'renderer' => 'Apptha\Airhotels\Block\Adminhtml\Renderersource\Uploadimage',
            ]
        );
$this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'class' => 'status'
            ]
        );
$this->addColumn(
            'created_at',
            [
                'header' => __('Created at'),
                'index' => 'created_at',
                'type' => 'date',
            ]
        );
        $this->addColumn('action', array(
        'header' => __('Action'),
        'width' => '100',
        'type' => 'action',
        'getter' => 'getId',
        'actions' => array(
            array(
                'caption' => __('Edit'),
                'url' => array('base' => 'airhotelsadmin/*/edit/id/'),
                'field' => 'id'
            )
        ),
        'filter' => false,
        'sortable' => false,
        'index' => 'stores',
        'is_system' => true,
    ));
    $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }
     /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => __('Delete'),
                'url' => $this->getUrl('airhotelsadmin/*/massDelete'),
                'confirm' => __('Are you sure?')
            )
        );
        return $this;
    }
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('airhotelsadmin/*/index', ['_current' => true]);
    }
    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'airhotelsadmin/*/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }
}