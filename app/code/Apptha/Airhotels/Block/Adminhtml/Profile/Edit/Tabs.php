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
* */
namespace Apptha\Airhotels\Block\Adminhtml\Profile\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs {
    /**
     * Construct class for seeting profile edit tabs
     *
     * @see \Magento\Framework\View\Element\Template::_construct()
     */
    protected function _construct() {
        parent::_construct ();
        $this->setDestElementId ( 'edit_form' );
        $this->setTitle ( __ ( 'Edit Profile' ) );
        $this->setId ( 'profile_edit_tabs' );   
    }
    /**
     * Get before html content
     *
     * @see \Magento\Backend\Block\Widget\Tabs::_beforeToHtml()
     */
    protected function _beforeToHtml() {
        $this->addTab ( 'Profile', ['label' => __ ( 'Edit Profile' ),
                'content' => $this->getLayout ()->createBlock ( 'Apptha\Airhotels\Block\Adminhtml\Profile\Edit\Tab\Info' )->toHtml (),
                'title' => __ ( 'Edit Profile' ),
                'active' => true] );
        return parent::_beforeToHtml ();
    }
}