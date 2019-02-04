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
class Accountverification extends \Magento\Framework\View\Element\Template{
       /**
        * Get current customer details
        * @var object
        */
       protected $customerSession;
       /**
        * Country list
        * @var object
        */
       protected $_countryFactory;
       /**
        * verified host collection
        * @var object
        */
       protected $_verifyhost;
       
       /**
        * __construct to load the model collection
        * @param \Magento\Customer\Model\Session $customerSession
        * @param \Magento\Framework\View\Element\Template\Context $context
        * @param \Apptha\Airhotels\Model\Verifyhost $verifyhost
        * @param array $data
        */
       public function __construct(\Magento\Customer\Model\Session $customerSession,
                     \Magento\Framework\View\Element\Template\Context $context,
                     \Apptha\Airhotels\Model\Verifyhost $verifyhost,
                     \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
                     array $data = []){
                            $this->customerSession = $customerSession;
                            $this->_countryFactory = $countryCollectionFactory;
                            $this->_verifyhost = $verifyhost;
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
        * To get verify host collection
        * @return object
        */
       public function getVerifyhost(){
              return $this->_verifyhost->load ( $this->getCustomerData()->getCustomer()->getId(), 'host_id' );
       }
       /**
        * To create the country collection
        * @return object
        */
       public function getCountryCollection(){
              return $this->_countryFactory->create()->loadByStore();
       }
       /**
        * To convert the country object to array
        * @return array
        */
       public function getCountryList(){
              return $this->getCountryCollection()->toOptionArray(false);
       }
       
       /**
        * post save verify url
        * @return string
        */
       public function getSaveVerificationUrl(){
              return $this->getUrl('airhotels/profile/saveverification');
       }
       
       /**
        * To return the document image saved path
        * @return string
        */
       public function getDocumentImage(){
              $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
              return $objectModelManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'Airhotels/Verificationimage';
       }
}