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
namespace Apptha\Airhotels\Controller\Listing;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * This class contains loading product image functions
 */
class Imageupload extends \Magento\Framework\App\Action\Action {
    /**
     *
     * @var $storeManager,
     * @var $resultRawFactory
     */
    protected $resultRawFactory;
    protected $storeManager;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Store\Model\StoreManagerInterface $storeManager) {
        parent::__construct ( $context );
        $this->resultRawFactory = $resultRawFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute the product images
     *
     * @return html
     */
    public function execute() {
        /**
         * To declare html result variable
         */
        $htmlResult = '';
        /**
         * To prepare image count
         */

        $imageCount = count ( $_FILES ['product_images'] ['name'] );
        /**
         * Set image increment zero
         */
        $imageInc = 0;
        /**
         * Iterate the product image files
         */
        foreach ( $_FILES ['product_images'] ['name'] as $key => $value ) {
            if ($imageInc < $imageCount && isset ( $key )) {
                $_FILES ['image_' . $imageInc] ['name'] = $_FILES ['product_images'] ['name'] [$imageInc];
                $_FILES ['image_' . $imageInc] ['type'] = $_FILES ['product_images'] ['type'] [$imageInc];
                $_FILES ['image_' . $imageInc] ['tmp_name'] = $_FILES ['product_images'] ['tmp_name'] [$imageInc];
                $_FILES ['image_' . $imageInc] ['error'] = $_FILES ['product_images'] ['error'] [$imageInc];
                $_FILES ['image_' . $imageInc] ['size'] = $_FILES ['product_images'] ['size'] [$imageInc];
                $filename = $_FILES ['product_images'] ['name'] [$imageInc];
                $filesize = $_FILES ['product_images'] ['size'] [$imageInc];
                $filetype = $_FILES ['product_images'] ['type'] [$imageInc];
                $imageType = array("image/jpg","image/jpeg","image/png");
                if (!in_array($filetype, $imageType) )
                {
                    echo __ ( $filename .' is not allowed <br>' ) ;
                    return;
                }
                list($width) = getimagesize($_FILES ['product_images'] ['tmp_name'] [$imageInc]);

                if( $width < 1400 ){
                    echo __ (' Upload the Image with minimum 1400px width'  );
                    return;
                }
                if( $filesize > 2000000 ){
                    echo __ ( $filename .' exceeds limit of 2 MB <br>'  );
                    return;
                }
                /**
                 * To increment the product image count
                 */
                $imageInc = $imageInc + 1;
            } else {
                break;
            }
        }
        /**
         * Checking for product image exist or not
         */
        if (isset ( $_FILES ['product_images'] ) && isset ( $_FILES ['product_images'] ['name'] )) {
            /**
             * Iterate for product image
             */
            for($inc = 0; $inc < $imageCount; $inc ++) {
                /**
                 * Create uploader object
                 */
                $uploaderObject = $this->_objectManager->create ( 'Magento\MediaStorage\Model\File\Uploader', [
                        'fileId' => 'image_' . $inc
                ] );
                /**
                 * Set option for uploader object
                 */
                $uploaderObject->setAllowedExtensions ( [
                        'jpg',
                        'jpeg',
                        'gif',
                        'png'
                ] );
                /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
                $imageAdapter = $this->_objectManager->get ( 'Magento\Framework\Image\AdapterFactory' )->create ();
                $uploaderObject->addValidateCallback ( 'catalog_product_image', $imageAdapter, 'validateUploadFile' );
                $uploaderObject->setAllowRenameFiles ( true );
                $uploaderObject->setFilesDispersion ( true );
                /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->_objectManager->get ( 'Magento\Framework\Filesystem' )->getDirectoryRead ( DirectoryList::MEDIA );
                $result = $uploaderObject->save ( $mediaDirectory->getAbsolutePath ( 'tmp/catalog/product' ) );
                /**
                 * To unset result
                 */
                unset ( $result ['tmp_name'] );
                unset ( $result ['path'] );
                /**
                 * Getting result url
                 */
                $result ['url'] = $this->_objectManager->get ( 'Magento\Catalog\Model\Product\Media\Config' )->getTmpMediaUrl ( $result ['file'] );
                $fileName = $result ['file'];
                $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
                /**
                 * Geting a absolute path for image upload
                 */
                $absPath = $objectModelManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'tmp/catalog/product' . $fileName;

                /**
                 * Create html result for image
                 */
                $htmlResult = $htmlResult . '<div class="listing-photo-image"><div class="image_close">x</div>
    <div class="base_image_container">
     <input class="base_image" type="radio" name="base_images" value="' . $fileName . '"><span>Album Cover</span>
     </div>
    <img src="' . $absPath . '" alt="' . $absPath . '" height="200" width="200">
    <input class="hidden_uploaded_image_path" type="hidden" name="images_path[]" value="' . $fileName . '" /></div>';
            }
        }
        /**
         * To return html result
         */
        echo $htmlResult;
    }
}