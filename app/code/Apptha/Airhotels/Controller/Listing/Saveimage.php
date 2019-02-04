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

use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory as CustomOptionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
/**
 * This class contains product save functions
 */
class Saveimage extends \Magento\Framework\App\Action\Action {

    /**
     *
     * @var CustomOptionFactory
     */
    protected $resultPageFactory;
    protected $productRepository;
    protected $productFactory;
    protected $customOptionFactory;
    protected $_file;
    protected $request;
    const XML_PATH_PRODUCT_APPROVAL = 'airhotels/product/product_approval';
    private $_save;

    /**
     * Constructor
     * \Magento\Framework\View\Result\PageFactory $resultPageFactory,
     * \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * \Magento\Catalog\Model\ProductFactory $productFactory
     * CustomOptionFactory $customOptionFactory
     */
    public function __construct(\Magento\Framework\App\Action\Context $context,\Magento\Framework\App\Request\Http $request, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Model\ProductFactory $productFactory, CustomOptionFactory $customOptionFactory, \Apptha\Airhotels\Controller\Listing\Basicsave $save, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,\Magento\Framework\Filesystem\Driver\File $file) {
        $this->resultPageFactory = $resultPageFactory;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $this->customOptionFactory = $customOptionFactory;
        $this->_file = $file;
        $this->_save = $save;
        parent::__construct ( $context );
    }

    /**
     * Execute the save product function
     *
     * @return object
     */
    public function execute() {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $productApproval = $this->scopeConfig->getValue ( static::XML_PATH_PRODUCT_APPROVAL, $storeScope );
        $customerSession = $this->objectManager->get ( 'Magento\Customer\Model\Session' );
        $productId = $customerSession->getCurrentExperienceId();
        $product = $this->objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
        $imagesPaths = array ();
        $imagesPaths = $this->request->getParam ( 'images_path' );
        $this->saveImageForProduct ( $product->getId (), $imagesPaths );
        $removeImageIds = $this->request->getParam ( 'remove_image' );
        $this->removeImageForProduct ( $product->getId (), $removeImageIds );
        $baseImage = $this->request->getParam ('base_image' );
        if(!empty($baseImage)){
        $this->baseImageForProduct ( $product->getId (), $baseImage );
        }else {
            $this->baseImageForProduct ( $product->getId (), $imagesPaths[0] );
        }

        $this->_eventManager->dispatch ( 'controller_action_catalog_product_save_entity_after', [
                'controller' => $this,
                'product' => $product
        ] );
        if (! empty ( $productId )) {
        $this->messageManager->addSuccess ( __ ( 'You updated the Listing.' ) );
        }
        if ($productApproval == 1) {
               $product->setPropertyApproved ( 1 );
               $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        } else {
               $product->setPropertyApproved ( 0 );
               $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
        }
        $product->save();
        //for sending  property awaiting  approval
           if (!empty ( $productId ) && $product->getPropertyApproved()==0) {
            $this->_save->sendAwaitingApprovalEmail($product);
           }

        $this->_redirect ( 'booking/listing/manage' );
    }

    /**
     * Save images for product
     *
     * @param int $productId
     * @param array $imagesPaths
     * @return void
     */
    public function saveImageForProduct($productId, $imagesPaths) {
        if (count ( $imagesPaths ) >= 1) {
            array_unique ( $imagesPaths );
            $productImage = $this->objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            $images = [ ];
            $inc = 1;
            foreach ( $imagesPaths as $path ) {
                $length = 10;
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen ( $characters );
                $randomString = '';
                for($i = 0; $i < $length; $i ++) {
                    $randomString .= $characters [rand ( 0, $charactersLength - 1 )];
                }
                $randomStringArr = array (
                        "position" => $inc,
                        "media_type" => "image",
                        "video_provider" => "",
                        "file" => $path,
                        "value_id" => "",
                        "label" => "",
                        "disabled" => 0,
                        "removed" => "",
                        "video_url" => "",
                        "video_title" => "",
                        "video_description" => "",
                        "video_metadata" => "",
                        "role" => ""
                );
                $images [$randomString] = $randomStringArr;
                $inc = $inc + 1;
            }
            $productImage->setData ( 'media_gallery', [
                    'images' => $images
            ] );
            $productImage->save ();
        } else {
            $productImage = $this->objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            $productImage = $productImage->setStatus(2);
            $productImage->save();
        }
    }

    /**
     * Removing existing images from product
     *
     * @param
     *            int product id
     * @param array $removeImageIds
     * @return void
     */
    public function removeImageForProduct($productId, $imagesIds) {
        if (count ( $imagesIds ) < 1) {
            return ;
        }
            array_unique ( $imagesIds );
            $product = $this->objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            $images = $product->getMediaGalleryImages ();
            $objectGallery = $this->objectManager->get ( 'Magento\Catalog\Model\ResourceModel\Product\Gallery' );
            $objectGallery->deleteGallery ( $imagesIds );
            $mediaDirectory = $this->objectManager->get ( 'Magento\Framework\Filesystem' )->getDirectoryRead ( \Magento\Framework\App\Filesystem\DirectoryList::MEDIA );
            $mediaRootDir = $mediaDirectory->getAbsolutePath ();
            foreach ( $imagesIds as $image ) {
                foreach ( $images as $productImage ) {
                    if ($productImage ['id'] == $image) {
                        $imageFilePath = $productImage ['file'];
                        $mediaRootDirectory = $mediaRootDir . 'catalog/product';
                        if ($this->_file->isExists ( $mediaRootDirectory . $imageFilePath )) {
                            $this->_file->deleteFile ( $mediaRootDirectory . $imageFilePath );
                        }
                    }
                }
            }

    }

    /**
     * Set base image
     *
     * @param int $productId
     * @param string $baseImage
     * @return void
     */
    public function baseImageForProduct($productId, $baseImage) {
        if (! empty ( $baseImage )) {
            $productBaseImage = $this->objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            $productBaseImage->setImage ( $baseImage );
            $productBaseImage->setSmallImage ( $baseImage );
            $productBaseImage->setThumbnail ( $baseImage );
            $productBaseImage->save ();
        }
    }

    /**
     * To get image info
     *
     * @param array $attributeCombinationArray
     * @param array $configurableProduct
     * @param array $imagesPaths
     * @param array $simpleProductImagesPath
     * @param array $usedPath
     * @param array $simpleProduct
     *
     * @return array
     */
    public function getImageInfo($attributeCombinationArray, $configurableProduct, $imagesPaths, $simpleProductImagesPath, $usedPath, $simpleProduct) {
        $baseImage = $productIdForImage = '';
        foreach ( $attributeCombinationArray as $value ) {
            if (isset ( $configurableProduct ['image_path'] [$value] )) {
                foreach ( $configurableProduct ['image_path'] [$value] as $imagePath ) {
                    $imagesPaths [] = $imagePath;
                    $simpleProductImagesPath [] = $imagePath;
                    if (in_array ( $imagePath, $usedPath )) {
                        $productIdForImage = array_search ( $imagePath, $usedPath );
                    } else {
                        $usedPath [$simpleProduct->getId ()] = $imagePath;
                    }
                }
            }
            if (isset ( $configurableProduct ['base_path'] [$value] )) {
                $baseImage = $configurableProduct ['base_path'] [$value];
            }
        }
        return array (
                'used_path' => $usedPath,
                'product_id_for_image' => $productIdForImage,
                'images_paths' => $imagesPaths,
                'simple_product_images_path' => $simpleProductImagesPath,
                'base_image' => $baseImage
        );
    }
    /**
     * Update image for products
     *
     * @param int $currentProductId
     * @param int $createdProductId
     *
     * @return void
     */
    public function updateImageValueByProduct($currentProductId, $createdProductId) {
        $productImages = $this->objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $createdProductId )->getMediaGalleryImages ();
        $currentProduct = $this->objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $currentProductId );
        if (count ( $productImages ) >= 1) {
            foreach ( $productImages as $productImage ) {
                $currentProduct->addImageToMediaGallery ( $productImage ['path'], array (
                        'image',
                        'small_image',
                        'thumbnail'
                ), false, false );
            }
            $currentProduct->save ();
        }
    }
    /**
     * Delete existing product images
     *
     * @param int $deleteImageProductId
     *
     * @return void
     */
    public function deleteExistingProductImages($deleteImageProductId) {
        $productImages = $this->objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $deleteImageProductId )->getMediaGalleryImages ();
        $deleteimagesIds = array ();
        foreach ( $productImages as $productImage ) {
            $deleteimagesIds [] = $productImage ['value_id'];
        }
        if (count ( $deleteimagesIds ) >= 1) {
            $this->removeImageForProduct ( $deleteImageProductId, $deleteimagesIds );
        }
    }
}