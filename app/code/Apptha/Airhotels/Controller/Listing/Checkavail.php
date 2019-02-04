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
namespace Apptha\Airhotels\Controller\Listing;

use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * This class contains loading category functions
 */
class Checkavail extends \Magento\Framework\App\Action\Action {

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
    protected $dataHelper;
    protected $resultJsonFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Apptha\Airhotels\Helper\Data $dataHelper, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory) {
        parent::__construct ( $context );
        $this->storeManager = $storeManager;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $this->dataHelper = $dataHelper;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute() {
        $listingParameters = $this->getRequest ()->getParams();
        $from = $this->objectManager->get('Apptha\Airhotels\Helper\Dateformat')->searchDateFormat($listingParameters['from']);
        $to = $this->objectManager->get('Apptha\Airhotels\Helper\Dateformat')->searchDateFormat($listingParameters['to']);       
        $listingParameters['from']=$from;
        $listingParameters['to']=$to;
        $this->objectManager->get('Apptha\Airhotels\Model\Checkavail')->checkavailable ($listingParameters);
    }
}
