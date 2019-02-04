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
class Massreject extends Verifyhosts {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Select host ids
         */
    $rejectIds = $this->getRequest ()->getParam ( 'selected' );
        foreach ( $rejectIds as $rejectId ) {
            try {
                /**
                 * Create Verify host object
                 */
                $VerifyHostRejectObj = $this->_objectManager->create ( 'Apptha\Airhotels\Model\Verifyhost' );
                /**
                 * To enable Verify host
                 */
                $VerifyHostRejectObj->load ( $rejectId )->setHostTags ( 2 )->save ();
                 /**
                 * To send email for host rejected
                 */
                $this->_objectManager->get ( 'Apptha\Airhotels\Helper\General' )->verifyHostMail ( $rejectId, 'rejected' );
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * Enabled host count
         */
        if (count ( $rejectIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were rejected.', count ( $rejectIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}