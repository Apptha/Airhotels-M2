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
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Checkout\Controller\Cart
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $response;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @codeCoverageIgnore
     */
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Checkout\Model\Session $checkoutSession,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
            \Magento\Checkout\Model\Cart $cart,
            \Magento\Framework\App\Response\Http $response,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory
            ) {
                parent::__construct(
                        $context,
                        $scopeConfig,
                        $checkoutSession,
                        $storeManager,
                        $formKeyValidator,
                        $cart
                        );
                $this->response = $response;
                $this->resultPageFactory = $resultPageFactory;
    }
    
    /**
     * Shopping cart display action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {  
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('checkout');
    }
}
