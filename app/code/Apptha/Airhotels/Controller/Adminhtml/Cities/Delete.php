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
namespace Apptha\Airhotels\Controller\Adminhtml\Cities;

use Apptha\Airhotels\Controller\Adminhtml\Cities;

class Delete extends Cities {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Getting delete id
         */
        $deleteId = $this->getRequest ()->getParam ( 'id' );
        try {
            /**
             * getting a object for cities
             */
            $citiesObj = $this->_objectManager->get ( '\Apptha\Airhotels\Model\City' );
            /**
             * Delete cities
             */
            $citiesObj->load ( $deleteId )->delete ();
        } catch ( Exception $e ) {
            $this->messageManager->addError ( $e->getMessage () );
        }
        /**
         * Setting a session success message and redirect to subscriptionplans grid page
         */
        $this->messageManager->addSuccess ( __ ( 'The data has been deleted successfully.' ) );
        $this->_redirect ( '*/*/index' );
    }
}