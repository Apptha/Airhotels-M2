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
namespace Apptha\Airhotels\Controller\Adminhtml\Profile;

use Apptha\Airhotels\Controller\Adminhtml\Profile;

class Save extends Profile {

       /**
        * Function to save Profile Data
        *
        * @return id(int)
        */
       public function execute() {
              $isPost = $this->getRequest ()->getPost ();
              if ($isPost) {
                     $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
                     $cusotmerProfileModel = $objectManager->get ( 'Apptha\Airhotels\Model\Customerprofile' );
                     $customerId = $this->getRequest ()->getPost ( 'id' );
                     if ($customerId) {
                            $cusotmerProfileModel->load ( $customerId );
                     }
                     try {
                     $formData = $this->getRequest ()->getPost ( );
                     $data=array();
                     foreach($formData as $key => $value){
                            $data[$key]=$value;
                     }
                     unset($data['id']);
                     unset($data['form_key']);
                     $cusotmerProfileModel->addData($data);
                     $cusotmerProfileModel->save ();
                      $customerAccount = $objectManager->get ( 'Magento\Customer\Model\Customer' )->load (  $customerId );
                    $customerAccount->setGender($cusotmerProfileModel->getGender());
                    $customerAccount->setDob($cusotmerProfileModel->getDob());
                    $customerAccount->save();
                     // Display success message
                     $this->messageManager->addSuccess ( __ ( 'Profile data has been saved.' ) );
                     // Check if 'Save and Continue'
                     if ($this->getRequest ()->getParam ( 'back' )) {
                            $this->_redirect ( '*/*/edit', [
                                          'id' => $cusotmerProfileModel->getId (),
                                          '_current' => true
                            ] );
                            return;
                     }
                     // Go to grid page
                     $this->_redirect ( '*/*/' );
                     return;
                     } catch ( \Exception $e ) {
                            $this->messageManager->addError ( $e->getMessage () );
                     }
                     $this->_getSession ()->setFormData ( $formData );
                     $this->_redirect ( '*/*/edit', [
                                   'id' => $customerId
                     ] );
              }
       }


}