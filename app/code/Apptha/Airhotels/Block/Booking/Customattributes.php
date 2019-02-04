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
 */
namespace Apptha\Airhotels\Block\Booking;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\StockRegistry;
use Zend\Form\Annotation\Object;


class Customattributes extends \Magento\Framework\View\Element\Template {

    /**
     * Add product custom attributes
     *
     * @param object $product
     * @param array $customAttributes
     * @param array $productData
     *
     * @return object $product
     */
    public function addCustomAttributes($product, $customAttributes, $productData) {
        $customAttributeArray = array ();
        foreach ( $customAttributes as $customAttribute ) {
            if (isset ( $productData [$customAttribute] )) {

                /**
                 * Save multi values
                 */
                if (is_array ( $productData [$customAttribute] )) {
                    $customAttributeArray [$customAttribute] = implode ( ',', $productData [$customAttribute] );
                } else {
                    $customAttributeArray [$customAttribute] = $productData [$customAttribute];
                }
            }
        }
        if (count ( $customAttributeArray ) >= 1) {
            $product->addData ( $customAttributeArray );
        }
        return $product;
    }
}