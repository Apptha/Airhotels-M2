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
namespace Apptha\Airhotels\Block\Profile;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Customer;
class Verification extends \Magento\Framework\View\Element\Template{
       /**
        * Get current customer details
        * @var object
        */
       protected $customerSession;
       protected $verifyhost;
       /**
        * __construct to load the model collection
        * @param \Magento\Customer\Model\Session $customerSession
        * @param \Magento\Framework\View\Element\Template\Context $context
        * @param array $data
        */
       public function __construct(\Magento\Customer\Model\Session $customerSession,
                     \Apptha\Airhotels\Model\Verifyhost $verifyhost,
                     \Magento\Framework\View\Element\Template\Context $context,
                     array $data = []){
                            $this->customerSession = $customerSession;
                            $this->verifyhost = $verifyhost;
                            parent::__construct($context,$data);
       }
       /**
        * To get the current customer collection
        * @return object
        */
       public function getCustomerData(){
              return $this->customerSession;
       }
       
    /**
        * get Account Verification Url
        * @return string
        */
       public function getAccountVerificationUrl(){
              return $this->getUrl('airhotels/profile/accountverification');
       }
   /**
     * To get the Host Verified collection
     * @return object
     */
    public function getHostVerified(){
           $customerData= $this->getCustomerData()->getCustomer();
           return $this->verifyhost->load ( $customerData->getId(), 'host_id' );
    }
       
}