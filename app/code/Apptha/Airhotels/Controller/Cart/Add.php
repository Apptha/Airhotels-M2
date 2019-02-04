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
namespace Apptha\Airhotels\Controller\Cart;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;

class Add extends \Magento\Checkout\Controller\Cart {
    /**
     *
     * @var ProductRepositoryInterface
     */
    protected $request;
    protected $_url;
    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     *            @codeCoverageIgnore
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Framework\App\Request\Http $request, CustomerCart $cart) {
        $this->cart = $cart;
        $this->checkoutSession = $checkoutSession;

        parent::__construct ( $context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart );
        $this->_request = $request;
        $this->_url = $context->getUrl();
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct() {
        $productId = ( int ) $this->getRequest ()->getParam ( 'product' );
        if ($productId) {
            $storeId = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getId ();
            try {
                return $this->_objectManager->get ( 'Magento\Catalog\Api\ProductRepositoryInterface' )->getById ( $productId, false, $storeId );
            } catch ( NoSuchEntityException $e ) {
                return false;
            }
        }
        return false;
    }

/**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(){
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        $params = $this->getRequest()->getParams();
        try {
            $this->cart->truncate();
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
            $product = $this->_initProduct();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $objectManager->get('Magento\Customer\Model\Session');
            if($product->getUserId()!= $customerSession->getCustomerId()){
            $related = $this->getRequest()->getParam('related_product');
            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }
            $this->cart->save();
            $quote = $this->cart->getQuote();
            $baseCurrencyCode = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore()->getBaseCurrencyCode();
            $currentCurrencyCode = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore()->getCurrentCurrencyCode();
            $this->checkoutSession->setAnyBaseSubtotal ( $this->getRequest()->getPost('subtotal_amt') );
            $this->checkoutSession->setFromdate ( $this->getRequest()->getPost('from') );
            $this->checkoutSession->setTodate ( $this->getRequest()->getPost('to') );
            $this->checkoutSession->setAccomodate ( $this->getRequest()->getPost('guests') );
            $this->checkoutSession->setProdId ( $this->getRequest()->getPost('product') );
            $this->checkoutSession->setServiceFee ( $this->getRequest()->getPost('serviceFee') );
            $this->checkoutSession->setRedirectUrl( $this->_url->getUrl('checkout'));
            foreach($quote->getAllVisibleItems() as $item){
            if ($baseCurrencyCode !== $currentCurrencyCode) {
                /**
                 * getting the base currency value
                 */
                $rateToBase = $this->_objectManager->get('\Magento\Directory\Model\CurrencyFactory')->create()->load($currentCurrencyCode)->getAnyRate($baseCurrencyCode);
                $baseCurrencyPrice = $this->checkoutSession->getAnyBaseSubtotal () * $rateToBase;
                $currentCurrencyPrice = $this->checkoutSession->getAnyBaseSubtotal ();
                $item->setRowTotal ( $currentCurrencyPrice );
                /**
                 * Set the base currency price value to Baserow total
                 */
                $item->setBaseRowTotal ( $baseCurrencyPrice );
                $item->save();
            } else {
                /**
                 * Set the basetotal price value to RowTotal.
                 */
                $item->setRowTotal ( $this->checkoutSession->getAnyBaseSubtotal () );
                /**
                 * Set the base currency price value to Baserow total
                 */
                $item->setBaseRowTotal ( $this->checkoutSession->getAnyBaseSubtotal () );
                $item->save();
            }
            }
            $quote->save();
            /**
             * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
             */
            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );
            $url = $this->_checkoutSession->getRedirectUrl(true);
            if (!$url) {
                $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }
            } else {
                $this->messageManager->addError( __('Host Can\'t Book His Own Property'));
                $url = $this->_redirect->getRedirectUrl($this->_redirect->getRefererUrl());
            }
            return $this->goBack($url);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice(
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                );
            }
            $url = $this->_checkoutSession->getRedirectUrl(true);
            if (!$url) {
                $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->goBack();
        }
    }

    protected function goBack($backUrl = null, $product = null)
    {
        if (!$this->getRequest()->isAjax()) {
            return parent::_goBack($backUrl);
        }

        $result = [];

        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                        'statusText' => __('Out of stock')
                ];
            }
        }

        $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
                );
    }


}
