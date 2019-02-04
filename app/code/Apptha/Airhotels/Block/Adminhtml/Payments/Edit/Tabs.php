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
 * */
namespace Apptha\Airhotels\Block\Adminhtml\Payments\Edit;
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
/**
 * This class contains tab functions
 * @author user
 *
 */
class Tabs extends WidgetTabs {
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $this->setDestElementId ( 'edit_form' );
        $this->setId ( 'payments_edit_tabs' );
       $this->setTitle ( __ ( 'Host Payments' ) );
    }
    /**
     * To prepare before to html content
     *
     * @see \Magento\Backend\Block\Widget\Tabs::_beforeToHtml()
     * @return object
     */
    protected function _beforeToHtml() {
        $this->addTab ( 'Payments', [  'label' => __ ( 'Pay Host Payments' ),'title' => __ ( 'Host Payments' ),
                'content' => $this->getLayout ()->createBlock ( 'Apptha\Airhotels\Block\Adminhtml\Payments\Edit\Tab\Form' )->toHtml (),'active' => true
        ] );
        return parent::_beforeToHtml ();
    }
}