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
namespace Apptha\Airhotels\Block\Message;

class Inbox extends \Magento\Framework\View\Element\Template
{

    /**
     *
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    protected $inboxCollection;

    protected $customerSession;

    protected $customerreply;
    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory
     * @param \Apptha\Airhotels\Model\ResourceModel\City\CollectionFactory $cityCollection
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $collectionFactory, \Apptha\Airhotels\Model\ResourceModel\Contacthost\CollectionFactory $inboxCollection, \Magento\Customer\Model\Customer $customers, \Magento\Customer\Model\Session $customerSession, \Apptha\Airhotels\Model\ResourceModel\Customerreply\CollectionFactory $customerreply)
    {
        $this->_collectionFactory = $collectionFactory;
        $this->_customers = $customers;
        $this->_customerreply = $customerreply;
        $this->_inboxCollection = $inboxCollection;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Getting most viewed products
     *
     * @return Array
     */
    public function getInboxDetails()
    {
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        // get values of current limit
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;
        $hostId = $this->customerSession->getCustomer()->getId();
        $collection = $this->_inboxCollection->create()->addFieldToFilter('is_receiver_delete', 0);
        $collection->addFieldToFilter(array(
            'receiver_id',
            'reply_message_id'
        ), array(
            array(
                'eq' => $hostId
            ),
            array(
                'eq' => $hostId
            )
        ));
        $collection->setOrder('created_at','DESC');
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    /**
     * Getting media url
     *
     * @return string
     */
    public function getMediaUrl()
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $currentStore = $storeManager->getStore();
        return $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Getting Customer details
     * 
     * @param
     *            $customerId
     * @return Array
     */
    public function getCustomer($customerId)
    {
        // Get customer by customerID
        return $this->_customers->load($customerId);
    }

    /**
     * Getting reply unread message count
     * 
     * @param
     *            $messageId
     * @return int
     */
    public function getReplyMessageCount($messageId)
    {   $replyMessage  = array();
        $hostId = $this->customerSession->getId();
        $contactHost = $this->_inboxCollection->create()
            ->addFieldToFilter('read_flag', 0)
            ->addFieldToFilter('receiver_id', $hostId)
            ->addFieldToFilter('id', $messageId);
        $custmerReply = $this->_customerreply->create()
            ->addFieldToFilter('receiver_id', $hostId)
            ->addFieldToFilter('message_id', $messageId);
        foreach ($custmerReply->getData() as $key =>$_custmerReply){
            $replyMessage [] = $_custmerReply;
            $replyMessage[$key]['flag'] = 0;
        }
        $total = $custmerReply->getSize() + $contactHost->getSize();
        if ($total != 0) {
            return array (
            'totalcount' => $total, 
            'message' => $replyMessage );
        } else {
            return $total;
        }
    }

    /**
     * Prepare layout for contact host inbox details
     *
     * @return object $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getInboxDetails()) {
            $pager = $this->getLayout()
                ->createBlock('Magento\Theme\Block\Html\Pager', 'airhotels.inbox.pager')
                ->setAvailableLimit(array(
                10 => 10,
                20 => 20,
                30 => 30,
                40 => 40
            ))
                ->setShowPerPage(true)
                ->setCollection($this->getInboxDetails());
            $this->setChild('pager', $pager);
            $this->getInboxDetails()->load();
        }
        return $this;
    }

    /**
     * Get Manage inbox pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Getting reply unread message count
     * 
     * @param
     *            $messageId
     * @return int
     */
    public function getReplyMessage()
    {
        $hostId = $this->customerSession->getId();
        return $this->_customerreply->create()
            ->addFieldToFilter('is_read', 0)
            ->addFieldToFilter('receiver_id', $hostId)
            ->addFieldToFilter('message_id', $messageId);
    }
    /**
      * Getting Customer Id
      * 
      * @return int
      */
    public function getCustomerId(){
        return $this->customerSession->getId();
    }
}