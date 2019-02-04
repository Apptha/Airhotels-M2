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
namespace Apptha\Airhotels\Controller\Review;

class Details extends \Magento\Framework\App\Action\Action {
      protected $reviewCollection;
      protected $request;
      
      /**
       * @param \Magento\Framework\App\Action\Context $context
       * @param \Apptha\Airhotels\Block\Product\Review $reviewDetails
       * @param \Magento\Framework\App\Request\Http $request
       */
      public function __construct(\Magento\Framework\App\Action\Context $context, \Apptha\Airhotels\Block\Product\Review $reviewDetails, \Magento\Framework\App\Request\Http $request) {
            parent::__construct ( $context );
            $this->context = $context;
            $this->reviewCollection = $reviewDetails;
            $this->request = $request;
      }
      
      /**
       * Function to load all listings for a review for the products
       */
      public function execute() {
            /**
             * Load layout for manage listings
             */
            $this->_view->loadLayout ();
            $this->_view->renderLayout ();
      }
}