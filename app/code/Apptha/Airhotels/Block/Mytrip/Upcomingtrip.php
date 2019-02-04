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
namespace Apptha\Airhotels\Block\Mytrip;

class Upcomingtrip extends \Magento\Framework\View\Element\Template
{

    protected $date;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Apptha\Airhotels\Model\Hostorder $hostOrderCollection)
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->date = $date;
        $this->_hostOrderCollection = $hostOrderCollection;
        parent::__construct($context);
    }

    /**
     * Show customer upcoming customer details
     */
    public function order()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $om->get('Magento\Customer\Model\Session');
        $todayDate = $this->date->gmtDate('Y-m-d');
        $customerId = $customerSession->getCustomer()->getId();
        // get values of current limit
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
        // get values of current page
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $collection = $this->_hostOrderCollection->getCollection()
            ->addFieldToFilter('fromdate', array(
            'gteq' => $todayDate
        ))
        ->addFieldToFilter('order_status',
                array(
                        array('like'=>'%complete%'),
                        array('like'=>'%processing%'),
                        array('like'=>'%cancelled%')
                ))
            ->addFieldToFilter('customer_id', $customerId)
            ->setOrder('fromdate', 'ASC');
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    /**
     * Get Manage previous trips pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Prepare layout for manage product
     *
     * @return object $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->order()) {
            $pager = $this->getLayout()
                ->createBlock('Magento\Theme\Block\Html\Pager', 'airhotels.previoustrip.pager')
                ->setAvailableLimit(array(
                5 => 5,
                10 => 10,
                15 => 15,
                20 => 20
            ))
                ->setShowPerPage(true)
                ->setCollection($this->order());
            $this->setChild('pager', $pager);
            $this->order()->load();
        }
        return $this;
    }
}
