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
namespace Apptha\Airhotels\Block\Adminhtml\Products\Grid\Renderer;
/**
 * This class contains product status functions for product grid
 * @author user
 *
 */
class Bookingtype extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $productId = $this->_getValue ( $row );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
        $bookingTypeId = $product->getBookingType();
        $attributeLabel = $product->getResource()->getAttribute('booking_type');
        if ($attributeLabel->usesSource()) {
        $optionText = $attributeLabel->getSource()->getOptionText($bookingTypeId);
        }

        return $optionText;
    }
}