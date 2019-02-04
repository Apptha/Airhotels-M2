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
namespace Apptha\Airhotels\Controller\Adminhtml\Verifyhosts;

use Apptha\Airhotels\Controller\Adminhtml\Verifyhosts;

/**
 * This class contains the Verify host grid functionality
 */
class Grid extends Verifyhosts {
    /**
     * Prepare Verify host collection
     */
    protected function _prepareCollection() {
        /**
         * Getting factory collection for grid
         */
        $collection = $this->_gridFactory->create ()->getCollection ();
        /**
         * Setting collection for grid
         */
        $this->setCollection ( $collection );
        /**
         * Calling parent prepare collection function
         */
        parent::_prepareCollection ();
        return $this;
    }
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * To create request page
         */
        return $this->_resultPageFactory->create ();
    }
}