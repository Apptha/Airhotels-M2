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
namespace Apptha\Airhotels\Block\Adminhtml\Cities\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
/**
 * Class contains edit city functions
 */
class Info extends Generic implements TabInterface {
    /**
     *
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    /**
     * __construct to load the model collection
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(Context $context, Registry $registry,  FormFactory $formFactory, Config $wysiwygConfig, array $data = []) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct ( $context, $registry, $formFactory, $data );
    }
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return __ ( 'Edit City' );
    }
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel() {
        return __ ( 'Edit City data' );
    }
    /**
     * Edit cities form
     * {@inheritDoc}
     * @see \Magento\Backend\Block\Widget\Form::_prepareForm()
     */
    protected function _prepareForm() {
        /** @var $model \Apptha\Marketplace\Model\Seller */
        $model = $this->_coreRegistry->registry ( 'airhotels_cities' );
        $form = $this->_formFactory->create ();
        $data = $model->getData ();
        $fieldset = $form->addFieldset ( 'base_fieldset', [
                'legend' => __ ( 'Edit City Data' )
        ] );
        if ($model->getId ()) {
            $fieldset->addField ( 'id', 'hidden', [
                    'name' => 'id'
            ] );
        }
        $fieldset->addField ( 'name', 'text', [
                'name' => 'name',
                'title' => __('Name'),
                'label' => __ ( 'Name' )
        ] );
        $fieldset->addField ( 'description', 'textarea', [
                'name' => 'description',
                'label' => __ ( 'Description' ),
               'required' => true,
                'title' => __('Description')
                
        ] );
        $fieldset->addField ( 'status', 'select', [
               'title' => __('Status'),
                'name' => 'status',
                'label' => __ ( 'Status' ),
                'required' => true,
                'options' => ['1' => __('Yes'), '0' => __('No')]
        ] );
        
        $fieldset->addField('cityimages', 'image', array(
                'name'      => 'cityimages',
                'label'     => __('City Image'),
                'note' => 'Allowed image type : jpg, jpeg, gif, png <br/> Minimum Upload Image Size: 250px*250px',
                'title'     => __('City Image')
                
        ));
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