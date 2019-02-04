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
* */
namespace Apptha\Airhotels\Block\Adminhtml\Contacthost;
    /**
     * Class for Contacthost Edit
     */
    class Edit extends \Magento\Framework\View\Element\Template{
        /**
      *
      * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
      */
     protected $collectionFactory;
     /**
      *
      * @param \Magento\Framework\View\Element\Template\Context $context             
      * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory             
      * @param \Apptha\Airhotels\Model\ResourceModel\City\CollectionFactory $cityCollection             
      * @param array $data             
      */
     public function __construct(\Magento\Framework\View\Element\Template\Context $context, 
                                 \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $collectionFactory, 
                                 \Apptha\Airhotels\Model\ResourceModel\Contacthost\CollectionFactory $inboxCollection,
                                 \Magento\Customer\Model\Customer $customers,  \Magento\Customer\Model\Session $customerSession,
                                 \Apptha\Airhotels\Model\ResourceModel\Customerreply\CollectionFactory $customerreply ) {
         $this->_collectionFactory = $collectionFactory;
         $this->_inboxCollection = $inboxCollection;
         $this->customerSession = $customerSession;
         $this->_customers = $customers;
         $this->_customerreply = $customerreply;
         parent::__construct ( $context );
     }
     /**
      * Getting contact host host based details
      * 
      * @return Array
      */
        public function getProductDetails() {
             $productId = $this->getRequest ()->getParam ( 'id' );
             $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
             // get values of current limit
             $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 15;
             $collection = $this->_inboxCollection->create ()->addFieldToFilter ( 'product_id', $productId );
             $collection->addFieldToFilter ( 'is_admin_delete', array ('eq' => 0) );
             $collection->setPageSize($pageSize);
             $collection->setCurPage($page);
             return $collection;
        }
        /**
      * Getting Customer details
      * @param $customerId
      * @return Array
      */
    public function getCustomer($customerId)
    {
        //Get customer by customerID
        return $this->_customers->load($customerId);
    }
    /**
     * Prepare layout for contach host sent items
     *
     * @return object $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getProductDetails()) {
            $pager = $this->getLayout()
                ->createBlock('Magento\Theme\Block\Html\Pager', 'airhotels.sent.pager')
                ->setAvailableLimit(array(
                5 => 5,
                10 => 10,
                15 => 15,
                20 => 20
            ))
                ->setShowPerPage(true)
                ->setCollection($this->getProductDetails());
            $this->setChild('pager', $pager);
            $this->getProductDetails()->load();
        }
        return $this;
    }
    /**
     * Get Manage sent items pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}