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
namespace Apptha\Airhotels\Block\Adminhtml\Payout\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Customer\Model\Customer;
use Apptha\Airhotels\Model\System\Config\Countrylist;
/**
 * Class contains edit profile functions
 */
class Info extends Generic implements TabInterface {
    /**
     * 
     * @var \\Airhotels\Model\System\Config\Countrylist
     */
    protected $_country;
    /**
     * 
     * @var \Magento\Eav\Model\Config
     */
    protected $_configOption;
    /**
     * 
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customerModel;
    /**
     * __construct to load the model collection
     * @param Context $context
     * @param Registry $registry
     * @param Countrylist $countryList
     * @param FormFactory $formFactory
     * @param \Magento\Eav\Model\Config $configoption
     * @param Customer $customerModel
     * @param array $data
     */
    public function __construct(Context $context, Registry $registry, Countrylist $countryList, FormFactory $formFactory,\Magento\Eav\Model\Config  $configoption, Customer $customerModel,   array $data = []) {
        $this->_country = $countryList;
        $this->_configOption = $configoption;
        $this->_customerModel = $customerModel;
        parent::__construct ( $context, $registry, $formFactory, $data );
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel() {
        return __ ( 'Hoster Payment Status' );
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return __ ( 'Property Information' );
    }

    /**
     * Edit profile form
     * {@inheritDoc}
     * @see \Magento\Backend\Block\Widget\Form::_prepareForm()
     */
    protected function _prepareForm() {
        /** @var $model \Apptha\Marketplace\Model\Seller */
        $model = $this->_coreRegistry->registry ( 'airhotels_payoutedit' );
        $data = $model->getData ();
        $form = $this->_formFactory->create ();

        $fieldset = $form->addFieldset ( 'base_fieldset', [
                'legend' => __ ( 'Hoster Payment Status' )
        ] );
        if ($model->getId ()) {
            $fieldset->addField ( 'id', 'hidden', [
                    'name' => 'id'
            ] );
        }
        $fieldset->addField ( 'order_id', 'text', [
                 'label' => __ ( 'Order Id' ),
                'name' => 'order_id',
                'title' => __('Order Id'),
                  'readonly' => true
        ] );
        $fieldset->addField ( 'payment_status', 'select', [
                  'name' => 'payment_status',
                  'values' => array(0=>__ ('Not paid To Hoster'),2=>__ ('Refund To Guest'),3=>__ ('Paid To Hoster')),
                  'label' => __ ( 'Status' ),
                  'title' => __('Status'),
                  'required' => true
        ] );
        $fieldset->addField ( 'payment_comment', 'textarea', [
                  'name' => 'payment_comment',
                  'label' => __ ( 'Message' ),
                  'title' => __('Message'),
                  'required' => true
        ] );
        $form->setValues ( $data );
        $this->setForm ( $form );
        return parent::_prepareForm ();
    }
    /**
     *
     * {@inheritdoc}
     *
     */
    public function canShowTab() {
        return true;
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function isHidden() {
        return false;
    }
}