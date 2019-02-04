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
 * @version     1.3
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2018 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
namespace Apptha\Airhotels\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Logout implements ObserverInterface
{
    protected $customerloginFactory;
    
    public function __construct( \Apptha\Airhotels\Model\CustomerloginFactory $CustomerloginFactory, \Magento\Framework\Stdlib\DateTime\DateTime $date ) {
    
        $this->customerloginFactory = $CustomerloginFactory;
        $this->date = $date;
    }
    
    public function execute(EventObserver $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $date = $this->date->gmtDate();
        $customerData = $this->customerloginFactory->create()->getCollection();
        
        foreach ($customerData as $data){
            if($data->getCustomerId() == $customer->getEntityId() && $data->getIsActive() == 1){
                $data->setLogoutTime($date);
                $data->setIsActive('0');
            }
        }
        $customerData->save();
    }
}
