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
namespace Apptha\Airhotels\Controller\Adminhtml\Cities;

use Magento\Framework\App\Filesystem\DirectoryList;
use Apptha\Airhotels\Controller\Adminhtml\Cities;

class Save extends Cities {

    /**
     * Function to save City Data
     *
     * @return id(int)
     */
    public function execute() {
        $isPost = $this->getRequest ()->getPost ();
        if ($isPost) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $citiesObj = $objectManager->get ( 'Apptha\Airhotels\Model\City' );
            $Id = $this->getRequest ()->getPost ( 'id' );
            if ($Id) {
                $citiesObj->load ( $Id );
            }
            try {
            $formData = $this->getRequest ()->getPost ( );
            
            foreach ( $_FILES ['cityimages'] as $value ) {
                list($width,$height) = getimagesize($_FILES ['cityimages']['tmp_name']);
                if( $width < 250 ){
                    $this->messageManager->addError(__ (' Upload the Image with minimum 250px width'  ));
                    $this->_redirect ( '*/*/edit', [
                            'id' => $Id
                    ] );
                    return;
                }
                if( $height < 250 ){
                    $this->messageManager->addError(__ (' Upload the Image with minimum 250px width'  ));
                    $this->_redirect ( '*/*/edit', [
                            'id' => $Id
                    ] );
                    return;
                }
            }
            
            if (isset ( $_FILES ['cityimages'] ['name'] ) && $_FILES ['cityimages'] ['name'] != '') {
                $fileId = 'cityimages';
                $absolutePath = 'Airhotels'. DIRECTORY_SEPARATOR .'Cityimage';
                $logoName = $this->uploadCityImage ( $fileId, $absolutePath );
            }
            $data=array();
            foreach($formData as $key => $value){
                $data[$key]=$value;
            }
            if(!empty($logoName)){
                $data['images'] = $logoName;
            }else{
                unset($data['images']);
            }
            unset($data['form_key']);
            $citiesObj->addData($data);
            
                $citiesObj->save ();
                // Display success message
                $this->messageManager->addSuccess ( __ ( 'City data has been saved.' ) );
                // Check if 'Save and Continue'
                if ($this->getRequest ()->getParam ( 'back' )) {
                    $this->_redirect ( '*/*/edit', [
                            'id' => $citiesObj->getId (),
                            '_current' => true
                    ] );
                    return;
                }
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
            $this->_getSession ()->setFormData ( $formData );
            $this->_redirect ( '*/*/index' );
        }
    }
    
    /**
     * To upload cities image
     *
     * @param string $fileId
     * @param string $absolutePath
     *
     * @return string
     */
    public function uploadCityImage($fileId, $absolutePath) {
    
        $uploader = $this->_objectManager->create ( 'Magento\MediaStorage\Model\File\Uploader', ['fileId' => $fileId]);
        $uploader->setAllowedExtensions ( [
                'jpg',
                'jpeg',
                'gif',
                'png'
        ] );
        /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
        $imageAdapter = $this->_objectManager->get ( 'Magento\Framework\Image\AdapterFactory' )->create ();
    
        $uploader->addValidateCallback ( 'catalog_product_image', $imageAdapter, 'validateUploadFile' );
        $uploader->setAllowRenameFiles ( true );
        $uploader->setFilesDispersion ( true );
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->_objectManager->get ( 'Magento\Framework\Filesystem' )->getDirectoryRead ( DirectoryList::MEDIA );
        $result = $uploader->save ( $mediaDirectory->getAbsolutePath ( $absolutePath ) );
        unset ( $result ['tmp_name'] );
        unset ( $result ['path'] );
        $result ['url'] = $this->_objectManager->get ( 'Magento\Catalog\Model\Product\Media\Config' )->getTmpMediaUrl ( $result ['file'] );
        /**
         * Return cities image
         */
        return $result ['file'];
    }
}