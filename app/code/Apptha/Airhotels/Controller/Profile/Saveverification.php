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
namespace Apptha\Airhotels\Controller\Profile;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
class Saveverification extends \Magento\Framework\App\Action\Action{
/**
 * File uplaod factory collection
 * @var object
*/
  protected $_fileUploaderFactory;
       /**
        * Load the page collection
        * @var object
        */
       protected $_resultPageFactory;
       /**
        * Media path directory creation collection
        * @var object
        */
       protected $_mediaDirectory;
       /**
        * Get current customer details
        * @var object
        */
       protected $customerSession;
       /**
        * __construct to load the model collection
        * @param Context $context
        * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
        * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
        * @param Session $customerSession
        * @param \Magento\Framework\Filesystem $filesystem
        */
       public function __construct(Context $context,
                     \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                     \Magento\Framework\Filesystem $filesystem,
                     \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
                     Session $customerSession){
                         $this->customerSession = $customerSession;
                            $this->_resultPageFactory = $resultPageFactory;
                            $this->_fileUploaderFactory = $fileUploaderFactory;
                            $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                           parent::__construct($context);
       }

       /**
        * To save the verify host data in airhotels_verify_host table
        * {@inheritDoc}
        * @see \Magento\Framework\App\ActionInterface::execute()
        */
       public function execute() {
              if ($this->customerSession->isLoggedIn ()){
                     try {
                     if (isset ( $_FILES ['documentimage'] ['name'] ) && $_FILES ['documentimage'] ['name'] != '') {
                     $fileId = 'documentimage';
                     $absolutePath = 'Airhotels'. DIRECTORY_SEPARATOR .'Verificationimage';
                     $logoName = $this->uploadDocument ( $fileId, $absolutePath );
                     }
 $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
 $customerData=$this->customerSession->getCustomer();
                     /**
                      * To save the upload image to server directory
                      */
                     
                     $postData=$this->getRequest ()->getPost ();

                     $country = $postData['country'];
                     $idType = $postData['id_type'];
                     /**
                      * Load verify host collection
                      * @var object $verifyHostModel
                      */
                     $verifyHostModel = $objectManager->get ( 'Apptha\Airhotels\Model\Verifyhost' )->load ( $customerData->getId(), 'host_id' );
                     $verifyHostModel->setTagId (1);
                     $verifyHostModel->setHostId ($customerData->getId());
                     $verifyHostModel->setHostName ($customerData->getName() );
                     $verifyHostModel->setHostEmail ($customerData->getEmail() );
                     $verifyHostModel->setHostTags (0);
                     $verifyHostModel->setCountryCode ($country);
                     $verifyHostModel->setIdType ($idType);
                     if(isset($logoName) && !empty($logoName)){
                            $verifyHostModel->setFilePath ($logoName);
                     }
                     $verifyHostModel->save ();
                     $this->messageManager->addSuccess ( __ ( 'The host details has been updated successfully for verification.' ) );
                     } catch (\Exception $e) {
                            $this->messageManager->addError($e->getMessage());
                     }
                     $this->_redirect ( 'airhotels/profile/accountverification' );
              } else {
                     $resultPage = $this->resultRedirectFactory->create ();
                     $this->messageManager->addNotice(__("Login require for editing the profile. So please <i class='fa fa-lock'></i> login now and edit your post."));
                     $resultPage->setPath ( 'customer/account/login' );
              }
       }

       /**
        * To upload document
        *
        * @param string $fileId
        * @param string $absolutePath
        *
        * @return string
        */
       public function uploadDocument($fileId, $absolutePath) {
              $uploader = $this->_fileUploaderFactory->create([
                            'fileId' => $fileId
              ] );
              $uploader->setAllowedExtensions ( ['jpg','jpeg','gif','png'] );
              /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
              $imageAdapter =  $this->_objectManager->get ( 'Magento\Framework\Image\AdapterFactory' )->create ();
              $uploader->addValidateCallback ( 'catalog_product_image', $imageAdapter, 'validateUploadFile' );
              $uploader->setFilesDispersion ( true );
              $uploader->setAllowRenameFiles ( true );
               $result = $uploader->save ( $this->_mediaDirectory->getAbsolutePath($absolutePath) );
              unset ( $result ['path'] );
              unset ( $result ['tmp_name'] );
              return $result ['file'];
       }
}