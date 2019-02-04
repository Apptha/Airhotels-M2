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
namespace Apptha\Airhotels\Block\Adminhtml\Profile\Edit\Tab;

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
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
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
     * @param Config $wysiwygConfig
     * @param \Magento\Eav\Model\Config $configoption
     * @param Customer $customerModel
     * @param array $data
     */
    public function __construct(Context $context, Registry $registry, Countrylist $countryList, FormFactory $formFactory, Config $wysiwygConfig,\Magento\Eav\Model\Config  $configoption, Customer $customerModel,   array $data = []) {
        $this->_wysiwygConfig = $wysiwygConfig;
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
        return __ ( 'Edit Profile data' );
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return __ ( 'Edit Profile' );
    }

    /**
     * Edit profile form
     * {@inheritDoc}
     * @see \Magento\Backend\Block\Widget\Form::_prepareForm()
     */
    protected function _prepareForm() {
        /** @var $model \Apptha\Marketplace\Model\Seller */
        $model = $this->_coreRegistry->registry ( 'airhotels_customerprofile' );
        $data = $model->getData ();
        /**
         * Set cusotm data in edit form
         */
        $data['profileimage'] = $this->getCustomerProfileImage();
        $data['firstname'] = $this->getCustomerData()->getFirstname();
        $data['lastname'] = $this->getCustomerData()->getLastname();
        $data['email'] =  $this->getCustomerData()->getEmail();
        $form = $this->_formFactory->create ();

        $fieldset = $form->addFieldset ( 'base_fieldset', [
                'legend' => __ ( 'Edit Profile Data' )
        ] );
        if ($model->getId ()) {
            $fieldset->addField ( 'id', 'hidden', [
                    'name' => 'id'
            ] );
        }
        $fieldset->addField ( 'firstname', 'text', [
                 'label' => __ ( 'First Name' ),
                'name' => 'firstname',
                'title' => __('First Name'),
                'readonly' => true
        ] );
        $fieldset->addField ( 'lastname', 'text', [
                'name' => 'lastname',
                'label' => __ ( 'Last Name' ),
                'title' => __('Last Name'),
                'readonly' => true
        ] );
         $fieldset->addField ( 'email', 'text', [
                'name' => 'email',
                'label' => __ ( 'Email' ),
                'readonly' => true,
               'title' => __('Email')
        ] );
        $fieldset->addField ( 'phone', 'text', [
                'name' => 'phone',
              'required' => true,
                'label' => __ ( 'Phone Number' ),
                'title' => __('Phone Number')
                
        ] );
        $fieldset->addField ( 'description', 'textarea', [
                'name' => 'description',
                'label' => __ ( 'Description' ),
                'title' => __('Description'),
                'required' => true
        ] );
        $fieldset->addField ( 'city', 'text', [
                'name' => 'city',
                'label' => __ ( 'City' ),
                'title' => __('City'),
                'required' => true
        ] );
        $fieldset->addField ( 'country', 'select', [
                'name' => 'country',
                'label' => __ ( 'Country' ),
                'values' => $this->_country->toOptionArray (),
                'title' => __('Country'),
                'required' => true
        ]);
        
        $fieldset->addField ( 'gender', 'select', [
                'name' => 'gender',
                'values' => $this->_configOption->getAttribute('customer', 'gender')->getSource()->getAllOptions(),
                'label' => __ ( 'Gender' ),
                'title' => __('Gender'),
                'required' => true
        ] );
        $fieldset->addField('dob','date',['name' => 'dob',
                        'label' => __ ( 'Birth Date' ),
                         'title' => __('Birth Date'),
                         'required' => true,
                        'class' => 'validate-date',
                        'date_format' => 'MM/dd/yyyy']);
        
        $fieldset->addField ( 'bankdetails', 'textarea', [
                'name' => 'bankdetails',
                'label' => __ ( 'Bank details' ),
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
    
    /**
     * To return the customer profile image
     * @return string
     */
    private function getCustomerProfileImage(){
        return 'Airhotels/Customerprofileimage/Resized'.$this->_coreRegistry->registry ( 'airhotels_customerprofile' )->getProfileimage();
    }
    /**
     * To return the customer model data using customer id
     * @return string
     */
    private function getCustomerData(){
        return $this->_customerModel->load ($this->_coreRegistry->registry('airhotels_customerprofile')->getCustomerId());
    }
}