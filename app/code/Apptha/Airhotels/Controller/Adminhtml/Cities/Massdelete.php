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

/**
 * This class contains the cities mass delete functionality.
 */
class Massdelete extends Cities {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Getting cities id for mass delete
         */
        $enableIds = $this->getRequest ()->getParam ( 'selected' );
        /**
         * Iterate cities id
         */
        foreach ( $enableIds as $enableId ) {
            try {
                /**
                 * getting an object for cities
                 */
                $citiesObj = $this->_objectManager->get ( '\Apptha\Airhotels\Model\City' );
                /**
                 * Delete selected cities id
                 */
                $citiesObj->load ( $enableId )->delete ();
            } catch ( Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * Checking for cities count
         */
        if (count ( $enableIds )) {
            /**
             * Setting session message for cities delete
             */
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were deleted.', count ( $enableIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}