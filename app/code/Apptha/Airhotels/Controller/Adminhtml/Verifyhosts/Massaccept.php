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
 * This class contains mass Verify host enabled functionality
 */
class Massaccept extends Verifyhosts {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Select host ids
         */
        $acceptIds = $this->getRequest ()->getParam ( 'selected' );
        foreach ( $acceptIds as $acceptId ) {
            try {
                /**
                 * Create Verify host object
                 */
                $verifyHostObj = $this->_objectManager->create ( 'Apptha\Airhotels\Model\Verifyhost' );
                /**
                 * To enable Verify host
                 */
                $verifyHostObj->load ( $acceptId )->setHostTags ( 1 )->save ();
                /**
                 * To send email for host verified
                 */
                $this->_objectManager->get ( 'Apptha\Airhotels\Helper\General' )->verifyHostMail ( $acceptId, 'verified' );
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * Enabled host count
         */
        if (count ( $acceptIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were accepted.', count ( $acceptIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}