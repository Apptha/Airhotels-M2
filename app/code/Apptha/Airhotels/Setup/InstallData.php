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
namespace Apptha\Airhotels\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

class InstallData implements InstallDataInterface {
    protected $groupFactory;
    private $categorySetupFactory;
    /**
     *
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     *
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;
    /**
     *
     * @param GroupFactory $groupFactory
     */
    public function __construct(GroupFactory $groupFactory, CategorySetupFactory $categorySetupFactory, CustomerSetupFactory $customerSetupFactory, AttributeSetFactory $attributeSetFactory, EavSetupFactory $eavSetupFactory) {
        $this->groupFactory = $groupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }
    /**
     * (non-PHPdoc)
     *
     * @see \Apptha\Airhotels\Setup\InstallData::install()
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup ();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create ( [
                'setup' => $setup
        ] );
        $fieldList = ['price', 'special_price', 'tax_class_id','special_from_date', 'special_to_date','minimal_price','cost','tier_price'];
        foreach ( $fieldList as $field ) {
            $applyTo = explode ( ',', $eavSetup->getAttribute ( \Magento\Catalog\Model\Product::ENTITY, $field, 'apply_to' ) );
            if (! in_array ( \Apptha\Airhotels\Model\Product\Type::TYPE_ID, $applyTo )) {
                $applyTo [] = \Apptha\Airhotels\Model\Product\Type::TYPE_ID;
                $eavSetup->updateAttribute ( \Magento\Catalog\Model\Product::ENTITY, $field, 'apply_to', implode ( ',', $applyTo ) );
            }
        }
        $attributeSetup = $this->categorySetupFactory->create ( [
                'setup' => $setup
        ] );
        $attributeSetup->addAttributeGroup ( \Magento\Catalog\Model\Product::ENTITY, 'Default', 'Booking Information', 1000 );
        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'propertyaddress', [
                'type' => 'varchar','backend' => '', 'frontend' => '','label' => 'Property Address',
                'input' => 'text','class' => '', 'source' => '','group' => 'Product Details',
                'visible' => true, 'required' => false, 'user_defined' => false,
                'default' => '', 'searchable' => true, 'filterable' => true, 'comparable' => false,
                'visible_on_front' => true, 'used_in_product_listing' => true,
                'unique' => false, 'apply_to' => '' ] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'city', [
                'type' => 'varchar', 'backend' => '', 'frontend' => '','label' => 'City','input' => 'text','class' => '',
                'source' => '','group' => 'Product Details',
                'visible' => true,'required' => false,'user_defined' => false,'default' => '',
                'searchable' => true,'filterable' => true,'comparable' => false,'visible_on_front' => true,'used_in_product_listing' => true,'unique' => false, 'apply_to' => '' ] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'state', [
                'type' => 'varchar', 'backend' => '', 'frontend' => '','label' => 'State', 'input' => 'text','class' => '','source' => '',
                'group' => 'Product Details',
                'visible' => true,'required' => false, 'user_defined' => false, 'default' => '', 'searchable' => true,
                'filterable' => true, 'comparable' => false, 'visible_on_front' => true, 'used_in_product_listing' => true, 'unique' => false,
                'apply_to' => '' ] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'country', [
                'type' => 'varchar','backend' => '', 'frontend' => '','label' => 'Country','input' => 'text',
                'class' => '', 'source' => '','group' => 'Product Details',
                'visible' => true, 'required' => false,
                'user_defined' => false,'default' => '', 'searchable' => true, 'filterable' => true,
                'comparable' => false,'visible_on_front' => true, 'used_in_product_listing' => true,'unique' => false, 'apply_to' => ''] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'accommodate_minimum', [
                'type' => 'int','backend' => '','frontend' => '','label' => 'Accommodate Minimum','input' => 'text','class' => '', 'source' => '', 'group' => 'Product Details',
                'visible' => true,'required' => false,'user_defined' => false,'default' => '','searchable' => true,'filterable' => true,'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => '' ] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'accommodate_maximum', [
                'type' => 'int', 'backend' => '','frontend' => '',
                'label' => 'Accommodate Maximum','input' => 'text', 'class' => '', 'source' => '','group' => 'Product Details','visible' => true,'required' => false,'user_defined' => false,'default' => '','searchable' => true,'filterable' => true,'comparable' => false,'visible_on_front' => true,'used_in_product_listing' => true,'unique' => false, 'apply_to' => '' ] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'latitude', [
                'type' => 'varchar', 'backend' => '',
                'frontend' => '', 'label' => 'Latitude', 'input' => 'text','class' => '','source' => '','group' => 'Product Details','visible' => true, 'required' => false, 'user_defined' => false, 'default' => '','searchable' => true,'filterable' => true,'comparable' => false,
                'visible_on_front' => true, 'used_in_product_listing' => true,'unique' => false, 'apply_to' => ''] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'longitude', [
                'type' => 'varchar','backend' => '', 'frontend' => '','label' => 'Longitude','input' => 'text','class' => '','source' => '','group' => 'Product Details',
                'visible' => true,'required' => false, 'user_defined' => false,'default' => '','searchable' => true,'filterable' => true,'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true, 'unique' => false,'apply_to' => '' ] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'user_id', [
                'type' => 'varchar', 'backend' => '', 'frontend' => '','label' => 'User ID','input' => 'text','class' => '', 'source' => '','group' => 'Product Details',
                'visible', 'visible' => true,'required' => false,'user_defined' => false,  'default' => '','searchable' => true, 'filterable' => true,
                'comparable' => false, 'visible_on_front' => true,'used_in_product_listing' => true, 'unique' => false,  'apply_to' => ''  ] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'cancelpolicy', [
                'type' => 'int','backend' => '','frontend' => '','label' => 'Cancelpolicy','input' => 'select','class' => '','source' => 'Apptha\Airhotels\Model\Config\Source\Options',
                'group' => 'Booking Information','visible' => true,'required' => true,'user_defined' => false,'default' => '','searchable' => true,
                'filterable' => true,'comparable' => false,'visible_on_front' => true, 'used_in_product_listing' => true,'unique' => false,'apply_to' => '' ] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'property_approved', [
                'type' => 'int','backend' => '','frontend' => '','label' => 'Property Approved','input' => 'boolean', 'class' => '','source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'group' => 'Product Details', 'visible' => true,'required' => false,'user_defined' => false,'default' => '','searchable' => true,'filterable' => true,
                'comparable' => false,'visible_on_front' => true,'used_in_product_listing' => true,'unique' => false,'apply_to' => ''] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'minimum_days', [
                'type' => 'int','backend' => '', 'frontend' => '', 'label' => 'Minimum Days For Rent','input' => 'text', 'class' => '', 'source' => '',
                'group' => 'Booking Information','visible' => true, 'required' => false, 'user_defined' => false,'default' => '','searchable' => true,'filterable' => true,
                'comparable' => false,'visible_on_front' => true,'used_in_product_listing' => true,'unique' => false,'apply_to' => '' ] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'maximum_days', [
                'type' => 'int','backend' => '','frontend' => '','label' => 'Maximum Days For Rent','input' => 'text','class' => '','source' => '','group' => 'Booking Information',
                'visible' => true,'required' => false,'user_defined' => false,'default' => '','searchable' => true,'filterable' => true,'comparable' => false,
                'visible_on_front' => true,'used_in_product_listing' => true,'unique' => false, 'apply_to' => '' ] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'privacy', [
                'type' => 'int','backend' => '','frontend' => '', 'label' => 'privacy','input' => 'select','class' => '', 'option' => array ('value' => array (
                'Private' => array ( 0 => 'Private'),'Shared' => array (0 => 'Shared'),'Public' => array (0 => 'Public')),'order' => array ('Private' => '0','Shared' => '1','Public' => '2'
                 )),'group' => 'Booking Information','visible' => true,'required' => true,'user_defined' => false,'default' => '','searchable' => true,'filterable' => true,
                'comparable' => false,'visible_on_front' => true,'used_in_product_listing' => true,'unique' => false,'apply_to' => '' ] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'booking_type', [
                'type' => 'int','backend' => '', 'frontend' => '', 'label' => 'Booking Type','input' => 'select','class' => '', 'option' => array ('value' => array (
                'Apartment' => array (0 => 'Apartment'),'House' => array (0 => 'House'),'Cottage' => array (0 => 'Cottage')),'order' => array ('Apartment' => '0','House' => '1', 'Cottage' => '2'
                )),'group' => 'Booking Information','visible' => true,'required' => true,'user_defined' => false, 'default' => '','searchable' => true,'filterable' => true,
                'comparable' => false,'visible_on_front' => true,'used_in_product_listing' => true,'unique' => false,'apply_to' => '' ] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'amenity', [
                'type' => 'text','backend' => '','frontend' => '', 'label' => 'Amenity','input' => 'multiselect','class' => '','option' => array ('value' => array (
                 'Smoking' => array (0 => 'Smoking'),'Kitchen' => array (0 => 'Kitchen'),'RoomService' => array (0 => 'Room Service')),'order' => array ('Smoking' => '0','Kitchen' => '1','Roomservice' => '2'
                 )),'group' => 'Booking Information', 'visible' => true, 'required' => true,'user_defined' => false,'default' => '','searchable' => true,'filterable' => true,'comparable' => false,'visible_on_front' => true,'used_in_product_listing' => true,'unique' => false,'apply_to' => ''] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'bedtype', [
                'type' => 'int', 'backend' => '','frontend' => '','label' => 'Bed Type','input' => 'select','class' => '','option' => array ('value' => array ('Cushion Beds' => array (0 => 'Cushion Bed'),'Real Bed' => array (
                 0 => 'Real Bed'),'Air Beds' => array (0 => 'Air Beds')),'order' => array ('Cushion Beds' => '0','Real Bed' => '1','Air Beds' => '2')),'group' => 'Booking Information','visible' => true,'required' => true,'user_defined' => false,'default' => '','searchable' => true,'filterable' => true,
                'comparable' => false,'visible_on_front' => true,'used_in_product_listing' => true,'unique' => false,'apply_to' => ''] );

        $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'rooms', [
                'type' => 'int','backend' => '','frontend' => '','label' => 'Rooms','input' => 'select','class' => '','option' => array ('value' => array ('One' => array ( 0 => 'One'),'Two' => array (
                 0 => 'Two'),'Three' => array (0 => 'Three')),'order' => array ('One' => '0','Two' => '1','Three' => '2')),'group' => 'Booking Information','visible' => true,'required' => true,
                'user_defined' => false,'default' => '', 'searchable' => true,'filterable' => true,'comparable' => false,'visible_on_front' => true,'used_in_product_listing' => true,'unique' => false,'apply_to' => ''] );

         $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'languages', [
                 'type' => 'text','backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend','frontend' => '', 'label' => 'Host Languages','input' => 'multiselect','class' => '',
                 'option' => array ('value' => array ('English' => array (0 => 'English'),'Espaol' => array (0 => 'Espaol'),
                         'Franais' => array (0 => 'Franais'),'Italiano' => array (0 => 'Italiano')),'order' => array ('English' => '0','Espaol' => '1','Franais' => '2','Italiano' => '3'
                         )),'group' => 'Booking Information', 'visible' => true, 'required' => true,'user_defined' => false,'default' => '',
                 'searchable' => true,'filterable' => true,'comparable' => false,'visible_on_front' => true,'used_in_product_listing' => true,'unique' => false,
                 'apply_to' => ''] );

         $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'house_rules', [
                 'type' => 'text','backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend','frontend' => '', 'label' => 'House Rules','input' => 'multiselect','class' => '',
                 'option' => array ('value' => array ('Pets allowed' => array (0 => 'Pets allowed'),'Smoking allowed' => array (0 => 'Smoking allowed'),
                         'Suitable for events' => array (0 => 'Suitable for events')),'order' => array ('Pets allowed' => '0','Smoking allowed' => '1','Suitable for events' => '2'
                         )),'group' => 'Booking Information', 'visible' => true, 'required' => true,'user_defined' => false,'default' => '',
                 'searchable' => true,'filterable' => true,'comparable' => false,'visible_on_front' => true,'used_in_product_listing' => true,'unique' => false,
                 'apply_to' => ''] );
        $setup->endSetup ();
    }
}
