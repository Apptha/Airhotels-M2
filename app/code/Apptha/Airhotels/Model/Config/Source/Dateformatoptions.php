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
 * @version     1.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2017 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
namespace Apptha\Airhotels\Model\Config\Source;

class Dateformatoptions implements \Magento\Framework\Option\ArrayInterface {
     /**
      * To option array
      *
      * @return array
      */
     public function toOptionArray() {
          return array (
                    array (
                              'value' => 'MM/DD/YYYY',
                              'label' => 'MM/DD/YYYY' 
                    ),
                    array (
                              'value' => 'MM/YYYY/DD',
                              'label' => 'MM/YYYY/DD' 
                    ),
                    array (
                              'value' => 'DD/MM/YYYY',
                              'label' => 'DD/MM/YYYY' 
                    ),
                    array (
                              'value' => 'DD/YYYY/MM',
                              'label' => 'DD/YYYY/MM' 
                    ),
                    array (
                              'value' => 'YYYY/DD/MM',
                              'label' => 'YYYY/DD/MM' 
                    ),
                    array (
                              'value' => 'YYYY/MM/DD',
                              'label' => 'YYYY/MM/DD' 
                    ) 
          );
     }
}
