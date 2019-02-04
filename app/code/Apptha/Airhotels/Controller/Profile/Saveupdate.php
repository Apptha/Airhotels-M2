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
class Saveupdate extends \Magento\Framework\App\Action\Action{
    /**
     * Load the page collection
     * @var object
     */
    protected $_resultPageFactory;
    /**
     * Get current customer details
     * @var object
     */
    protected $customerSession;
    /**
     * Media path directory creation collection
     * @var object
     */
    protected $_mediaDirectory;
    /**
     * File uplaod factory collection
     * @var object
     */
    protected $_fileUploaderFactory;
    /**
     * __construct to load the model collection
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param Session $customerSession
     */
    public function __construct(Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Magento\Framework\Filesystem $filesystem,
            \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
            Session $customerSession){
        $this->_resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($context);
    }

    /**
     * To save the customer profile data in airhotel_customer_profile table
     * {@inheritDoc}
     * @see \Magento\Framework\App\ActionInterface::execute()
     */
    public function execute() {
         if ($this->customerSession->isLoggedIn ()){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $customerData=$this->customerSession->getCustomer();
            try {
                if (isset ( $_FILES ['profileimage'] ['name'] ) && $_FILES ['profileimage'] ['name'] != '') {
                        list($width,$height) = getimagesize($_FILES ['profileimage'] ['tmp_name']);
                        if( $width < 250 ){
                            $this->messageManager->addError(__ (' Upload the Image with minimum 250px width'  ));
                            $this->_redirect ( 'airhotels/profile/edit' );
                            return;
                        }
                        if( $height < 250 ){
                            $this->messageManager->addError(__ (' Upload the Image with minimum 250px width'  ));
                            $this->_redirect ( 'airhotels/profile/edit' );
                            return;
                        }
                    /**
                     * To save the upload image to server directory
                     */
                        $fileId = 'profileimage';
                        $absolutePath = 'Airhotels'. DIRECTORY_SEPARATOR .'Customerprofileimage';
                        $logoName = $this->uploadStoreLogo ( $fileId, $absolutePath );
                }
            $postData=$this->getRequest ()->getPost ();

            $dob = $objectManager->get('\Apptha\Airhotels\Helper\Dateformat')->searchDateFormat($postData['dob']);
            $phoneNumber = $postData['phonenumber'];
            $description = $postData['description'];
            $bankdetails = $postData['bankdetails'];
            $city = $postData['city'];
            $gender = $postData['gender'];
            $countryname = $postData['countryname'];
            /**
             * Load cusotmer profle collection
             * @var object $customerProfileModel
             */
            $customerProfileModel = $objectManager->get ( 'Apptha\Airhotels\Model\Customerprofile' )->load ( $customerData->getId(), 'customer_id' );
            $customerProfileModel->setGender ($gender);
            $customerProfileModel->setDob (date('Y-m-d', strtotime($dob)));
            $customerProfileModel->setPhone ($phoneNumber );
            $customerProfileModel->setDescription ($description );
            $customerProfileModel->setCity ($city);
            $customerProfileModel->setCountry ($countryname);
            $customerProfileModel->setBankdetails ($bankdetails);
            if(isset($logoName) && !empty($logoName)){
            $customerProfileModel->setProfileimage ($logoName);
            }
            $customerProfileModel->setCustomerId ($customerData->getId());
            $customerProfileModel->save ();
            $this->messageManager->addSuccess ( __ ( 'Customer profile details has been updated Successfully' ) );
            } catch (\Exception $e) {
                   $this->messageManager->addError($e->getMessage());
            }
            $this->_redirect ( 'airhotels/profile/edit' );

        } else {
            $resultPage = $this->resultRedirectFactory->create ();
            $this->messageManager->addNotice(__("Login Reuqire For Edit Profile. So Please <i class='fa fa-lock'></i> Login Now And Edit Your Post."));
            $resultPage->setPath ( 'customer/account/login' );
        }
    }

    /**
     * To upload store logo
     *
     * @param string $fileId
     * @param string $absolutePath
     *
     * @return string
     */
    public function uploadStoreLogo($fileId, $absolutePath) {
        $uploader = $this->_fileUploaderFactory->create([
                'fileId' => $fileId
        ] );
        $uploader->setAllowedExtensions ( ['jpg','jpeg','gif','png'] );
        /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
        $imageAdapter =  $this->_objectManager->get ( 'Magento\Framework\Image\AdapterFactory' )->create ();
        $uploader->addValidateCallback ( 'catalog_product_image', $imageAdapter, 'validateUploadFile' );
        $uploader->setAllowRenameFiles ( true );
        $uploader->setFilesDispersion ( true );
        $result = $uploader->save ( $this->_mediaDirectory->getAbsolutePath($absolutePath) );
        unset ( $result ['tmp_name'] );
        unset ( $result ['path'] );
        $result ['url'] = $this->_objectManager->get ( 'Magento\Catalog\Model\Product\Media\Config' )->getTmpMediaUrl ( $result ['file'] );
        $image = $result ['file'];
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $absPath = $objectModelManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . $absolutePath . $image;
        /**
         * To resize image
         */
        $imageResized = $this->_mediaDirectory->getAbsolutePath ( 'Airhotels/Customerprofileimage/Resized' ) . $image;
        $imageFactory = $objectModelManager->get ( 'Magento\Framework\Image\AdapterFactory' );
        $imageResize = $imageFactory->create ();
        $imageResize->open ( $absPath );
        $imageResize->constrainOnly ( TRUE );
        $imageResize->keepTransparency ( false );
        $imageResize->keepFrame ( FALSE );
        $imageResize->keepAspectRatio ( false );
        $imageResize->resize ( 250, 250 );
        $imageResize->save ( $imageResized );
        /**
         * Return store logo for save on cusotmer profile table
         */
        return $image;
    }
}
