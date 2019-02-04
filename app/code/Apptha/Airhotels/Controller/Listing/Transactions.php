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

/**
 * This class contains the host tr
 */
class Transactions extends \Magento\Framework\App\Action\Action {
    /**
     * Function to load host store page
     *
     * @return $array
     */
    public function execute() {

        /**
         * Check whether module enabled or not
         */
            $logedInUser = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
            $customerId = $logedInUser->getId ();

            if ($logedInUser->isLoggedIn ()) {
                $transactionId = $this->getRequest ()->getParam ( 'id' );
                if (! empty ( $transactionId )) {
                    $this->updateAcknowledgeForTransaction ( $customerId, $transactionId );
                }
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } else {
                    $this->_redirect ( 'customer/account' );
            }
    }

    /**
     * To update acknowledge for host transaction
     *
     * @param int $customerId
     * @param int $transactionId
     *
     * @return void
     */
    public function updateAcknowledgeForTransaction($customerId, $transactionId) {
        /**
         * Getting host payment by id
         */
        $hostPayments = $this->_objectManager->get ( 'Apptha\Airhotels\Model\Payments' )->load ( $transactionId );
        /**
         * Checking for host payment count
         */
        if (count ( $hostPayments ) >= 1) {
            /**
             * Get host id form host payment model
             */
            $hostId = $hostPayments->getHostId ();
            /**
             * Checking for host payments
             */
            if ($customerId == $hostId) {
                /**
                 * Getting date
                 */
                $date = $this->_objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
                /**
                 * Setting data to host payments
                 */
                $hostPayments->setIsAck ( 1 );
                $hostPayments->setAckAt ( $date );
                $hostPayments->save ();
                /**
                 * Seting session message for host
                 */
                $this->messageManager->addSuccess ( __ ( 'The transaction has been updated successfully.' ) );
            }
        }
    }
}
