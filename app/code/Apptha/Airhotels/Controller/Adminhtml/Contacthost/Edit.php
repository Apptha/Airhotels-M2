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
namespace Apptha\Airhotels\Controller\Adminhtml\Contacthost;

use Apptha\Airhotels\Controller\Adminhtml\Contacthost;
/**
 * Clas contains cities edit  form functions
**/
class Edit extends Contacthost {
    /**
     * Execute the contact host edit section to execute
     * 
     **/
    public function execute() {
        $id = $this->getRequest ()->getParam ( 'id' );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $model = $objectManager->get (  '\Magento\Catalog\Model\Product' );
        if ($id) {
            $model->load ($id);
            if (!$model->getId ()) {
                $this->messageManager->addError ( __ ( 'This products exists.' ) );
                $this->_redirect ( '*/*/' );
                return;
            } else{
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            }
        }
    }
}