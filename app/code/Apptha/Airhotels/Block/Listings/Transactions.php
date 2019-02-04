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

/**
 * This class used to display the products collection
 */
class Transactions extends \Magento\Framework\View\Element\Template {

    /**
     *
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     *
     * Manage order block construct
     *
     * @param Template\Context $context
     * @param ProductFactory $productFactory
     * @param array $data
     *
     * @return void
     */
    public function __construct(Template\Context $context, \Magento\Framework\Locale\CurrencyInterface $localeCurrency, \Magento\Framework\App\Request\Http $request, array $data = []) {
        $this->localeCurrency = $localeCurrency;
        $this->request = $request;
        parent::__construct ( $context, $data );
    }

    /**
     * Set product collection uisng ProductFactory object
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        /**
         * Creating object for customer session
         */
         /** get values of current page */
         $page = ($this->request->getParam('p'))? $this->request->getParam('p') : 1;
         /**  get values of current limit */
         $pageSize =  ($this->request->getParam('limit'))? $this->request->getParam('limit') : 10;
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectModelManager->get ( 'Magento\Customer\Model\Session' );
        /**
         * Declare customer id
         */
        $customerId='';
        if ($customerSession->isLoggedIn ()) {
            $customerId = $customerSession->getId ();
        }

        /**
         * Filter by host id
         */
        $collection = $objectModelManager->get ( 'Apptha\Airhotels\Model\Hostorder' )->getCollection ()->addFieldToSelect ( '*' );
        $collection->addFieldToFilter ( 'host_id', $customerId )->addFieldToFilter ( 'order_status', 'complete' );

        /**
         * Set order for manage order
         */
        $collection->setOrder ( 'id', 'desc' )->setPageSize ( $pageSize )->setCurPage ( $page );
        $this->setCollection ( $collection );
    }

    /**
     * Prepare layout for view host order
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        /**
         * Setting title for manage order
         */
        $this->pageConfig->getTitle ()->set ( __ ( "Transactions" ) );
        /**
         * Call perant prepare layout
         */
        parent::_prepareLayout ();
        /**
         *
         * @var \Magento\Theme\Block\Html\Pager
         */
        $pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'airhotels.transaction.manage.pager' );
        $pager->setAvailableLimit(array(
                10 => 10,
                20 => 20,
                50 => 50
            ))->setShowAmounts ( false )->setCollection ($this->getCollection ());
        $this->setChild ( 'pager', $pager );
        $this->getCollection ()->load ();
        /**
         * Return layout
         */
        return $this;
    }

    /**
     * Get currency symbol by code
     *
     * @param string $currencyCode
     *
     * @return string
     */
    public function getCurrencySymbol() {
        /**
         * To get currency symbol
         */
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $currencyCode = $objectModelManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseCurrencyCode ();
        if ($this->localeCurrency->getCurrency ( $currencyCode )->getSymbol ()) {
            $currencyCode = $this->localeCurrency->getCurrency ( $currencyCode )->getSymbol ();
        }
        return $currencyCode;
    }

    /**
     * Prepare Page Html
     *
     * @return string
     */
    public function getPagerHtml() {
        /**
         * To get child html
         */
        return $this->getChildHtml ( 'pager' );
    }

    /**
     * Get Payment request configuration value
     *
     * @return boolean
     */
    public function getPaymentRequestValue(){
       return $this->_scopeConfig->getValue('airhotels/general/payment_request', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
