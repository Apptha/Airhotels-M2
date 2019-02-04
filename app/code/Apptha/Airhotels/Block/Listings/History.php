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
namespace Apptha\Airhotels\Block\Listings;
class History extends \Magento\Framework\View\Element\Template{

    /**
     * Host order history Data
     * @return string     *
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     * @var \Apptha\Airhotels\Model\ResourceModel\City\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Apptha\Airhotels\Model\ResourceModel\Hostorder\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->request = $request;
         $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context, $data);
    }
    /**
    * Get Host property booking collection
    * return array
    */
    public function getHostOrderCollection()
    {
        /** get values of current page */
        $page = ($this->request->getParam('p'))? $this->request->getParam('p') : 1;
        /**  get values of current limit */
        $pageSize =  ($this->request->getParam('limit'))? $this->request->getParam('limit') : 10;
        $formData = $this->getRequest ()->getPost ();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();        
        $customerId = $objectManager->get('Magento\Customer\Model\Session')->getCustomer()->getId();
        $collection =$this->_collectionFactory->create();
        $collection->addFieldToFilter('host_id',$customerId);
        if(count($formData)!=0 && $formData['clear'] == 0)
        {
            $from = $objectManager->get('\Apptha\Airhotels\Helper\Dateformat')->searchDateFormat($formData['from']);
            $to = $objectManager->get('\Apptha\Airhotels\Helper\Dateformat')->searchDateFormat($formData['to']);
            $collection->addFieldToFilter('fromdate',array('gteq'=>$from));
            $collection->addFieldToFilter('fromdate',array('lteq'=>$to));
        }
        $collection->setOrder('order_id', 'DESC')->setPageSize ( $pageSize )->setCurPage ( $page );
        return $collection;
    }
    /**
    * Format price
    * return string
    */
    public function toCurrentCurrency($price){
       $toCurrency = $this->_storeManager->getStore()->getCurrentCurrency();
       return $this->_storeManager->getStore()->getBaseCurrency()->convert($price, $toCurrency);
   }
   /**
     * Getting most viewed products
     * @return Array
     */
    public function getCurrentCurrencySymbol(){
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $currencysymbol = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
       return $currencysymbol->getStore()->getCurrentCurrencyCode();
   }
   public function getOrderitemInfo($orderId)
   {
       $order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($orderId);
        return $order->getId();

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
        $pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'airhotels.order.history.pager' );
        $pager->setAvailableLimit(array(
                10 => 10,
                20 => 20,
                50 => 50
            ))->setShowAmounts ( false )->setCollection ($this->getHostOrderCollection ());
        $this->setChild ( 'pager', $pager );
        $this->getHostOrderCollection () ->load();
        return $this;
    }
    /**
      * display seller construct
      *
      * @return void
      */
     public function getHostOrderPagination() {
          /** get values of current page */
          $page = ($this->request->getParam('p'))? $this->request->getParam('p') : 1;
          /**  get values of current limit */
          $pageSize =  ($this->request->getParam('limit'))? $this->request->getParam('limit') : 10;
          return $this->getHostOrderCollection()->setPageSize ( $pageSize )->setCurPage ( $page );
     }
   /**
      * Get Manage product pager html
      *
      * @return string
      */
     public function getPagerHtml() {
          return $this->getChildHtml ( 'pager' );
     }
}
