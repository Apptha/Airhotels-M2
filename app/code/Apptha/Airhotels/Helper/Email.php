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
namespace Apptha\Airhotels\Helper;

/**
 * This class contains sending email functions
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper {
    /**
     * Email Template for Product Disapproval
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_PRODUCT_DISAPRROVAL_TEMPLATE = 'airhotels/product/disapproval_template';
    /**
     * Email Template for Product  Approval
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_PRODUCT_APPROVAL_TEMPLATE = 'airhotels/product/approval_template';
   /**
     * Email Template for Product  Delete
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_PRODUCT_DELETE_TEMPLATE = 'airhotels/product/admin_booking_delete_option';
    /**
     * Email Template for host new proprty added
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_HOST_NEW_PROPERTY = 'airhotels/product/new_booking_template';
    /**
     * Email Template for host awaiting for approval
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_HOST_LISTING_REQUEST = 'airhotels/product/host_booking_approval_template';
    /**
     * Email Template for host   order notification
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_HOST_ORDER_TEMPLATE = 'airhotels/order/notification_template';
    /**
     * Email Template for Guest cancel request
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_GUEST_CANCEL_REQUEST = 'airhotels/order/item_request_template';
    /**
     * Email Template for Host  cancel approve
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_HOST_CANCEL_APPROVE = 'airhotels/order/item_cancel_return_template';
    /**
     * Email Template for Contact Host 
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_CONTACT_HOST = 'airhotels/contact_host';
    /**
     * Email Template for Contact Host Reply
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_CONTACT_HOST_REPLY = 'airhotels/contact_host_reply';
    protected $_scopeConfig;
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     *
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    
    /**
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     *
     * @var string
     */
    protected $temp_id;
    
    /**
     *
     * @param Magento\Framework\App\Helper\Context $context            
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig            
     * @param Magento\Store\Model\StoreManagerInterface $storeManager            
     * @param Magento\Framework\Translate\Inline\StateInterface $inlineTranslation            
     * @param Magento\Framework\Mail\Template\TransportBuilder $transportBuilder            
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder) {
        $this->_scopeConfig = $context;
        parent::__construct ( $context );
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
    }
    
    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path            
     * @param int $storeId            
     * @return mixed
     */
    public function getConfigValue($path, $storeId) {
        /**
         * To getting the store based config
         */
        return $this->scopeConfig->getValue ( $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId );
    }
    
    /**
     * Function to get store
     *
     * @return Store
     */
    public function getStore() {
        /**
         * Get store details from store manger object
         */
        return $this->_storeManager->getStore ();
    }
    
    /**
     * Return template id according to store
     *
     * @return mixed
     */
    public function getTemplateId($xmlPath) {
        /**
         * To get the configurable values
         */
        return $this->getConfigValue ( $xmlPath, $this->getStore ()->getStoreId () );
    }
    /**
     * [generateTemplate description] with template file and tempaltes variables values
     * 
     * @param Mixed $emailTemplateVariables            
     * @param Mixed $senderInfo            
     * @param Mixed $receiverInfo            
     * @return void
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo, $templateId, $area,$ccEmail='') {
        /**
         * To set template indentifiler
         */
        $this->_transportBuilder->setTemplateIdentifier ( $templateId )->setTemplateOptions ( [ 
                'area' => $area,
                'store' => $this->_storeManager->getStore ()->getId () 
        ] )->setTemplateVars ( $emailTemplateVariables )->setFrom ( $senderInfo )->addTo ( $receiverInfo ['email'], $receiverInfo ['name'] );
        if($ccEmail){
             $this->_transportBuilder->addBcc($ccEmail);
        }
        /**
         * Return email template option
         */
        return $this;
    }
    
    /**
     * Send custom mail
     *
     * @param Mixed $emailTemplateVariables            
     * @param Mixed $senderInfo            
     * @param Mixed $receiverInfo            
     * @return void
     */
    public function yourCustomMailSendMethod($emailTemplateVariables, $senderInfo, $receiverInfo, $templateId,$ccEmail='') {
        /**
         * To declare all email template id
         */
        $productApprovalTemplate= 'airhotels_product_approval_template';
        $productDisapprovalTemplate  = 'airhotels_product_disapproval_template';
        $productDeleteTemplate  = 'airhotels_product_admin_booking_delete_option';
        $productHostDeleteTemplate  = 'airhotels_custom_email_propertydelete_template';
        $productReviewTemplate  = 'airhotels_customer_review_email_adminapproval_template';
        $hostVerifyTemplate  = 'airhotels_host_verify_email_template';
        $newProperty  ='airhotels_product_new_booking_template';
        $propertyAwaitingApproval='airhotels_product_host_booking_approval_template';
        $customerReviewModeration ='airhotels_customer_review_for_approval';
        $customerOrderCancel='airhotels_order_item_cancel_return_template';
        $customerOrderCancelReject='airhotels_customer_order_cancel_reject';
        $customerOrderCancelRequest='airhotels_order_item_request_template';
        $hostOrderCreditmemo='airhotels_host_order_creditmemo';
        $hostCustomerOrder='airhotels_order_notification_template';
        $hostCustomerInvoice='airhotels_host_order_invoice_create_after';
        $hostPaymentRequest='airhotels_host_payment_request';
        $adminPaidToHost='airhotels_admin_paid_money_host';
        $hostAcknowledge='airhotels_host_payment_acknowledge';
        $customerOrderStatus='airhotels_order_status';        
        
        /**
         * Checking for email template id
         */
        switch ($templateId) {
            case $productDisapprovalTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_EMAIL_PRODUCT_DISAPRROVAL_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $productApprovalTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_EMAIL_PRODUCT_APPROVAL_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $productDeleteTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_EMAIL_PRODUCT_DELETE_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $hostVerifyTemplate :
                $this->temp_id = $hostVerifyTemplate;
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $productHostDeleteTemplate :
                $this->temp_id = $productHostDeleteTemplate;
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $productReviewTemplate :
                $this->temp_id = $productReviewTemplate;
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $newProperty :
                $this->temp_id =  $this->temp_id = $this->getTemplateId ( static::XML_PATH_EMAIL_HOST_NEW_PROPERTY );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $propertyAwaitingApproval :
                $this->temp_id =  $this->temp_id = $this->getTemplateId ( static::XML_PATH_EMAIL_HOST_LISTING_REQUEST );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $customerReviewModeration :
                $this->temp_id = $customerReviewModeration;
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $customerOrderCancel :
                $this->temp_id =  $this->temp_id = $this->getTemplateId ( static::XML_PATH_EMAIL_HOST_CANCEL_APPROVE );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $customerOrderCancelReject :
                $this->temp_id = $customerOrderCancelReject;
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $customerOrderCancelRequest :
                $this->temp_id =  $this->temp_id = $this->getTemplateId ( static::XML_PATH_EMAIL_GUEST_CANCEL_REQUEST );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $hostOrderCreditmemo :
                $this->temp_id = $hostOrderCreditmemo;
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $hostCustomerOrder :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_EMAIL_HOST_ORDER_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $hostCustomerInvoice :
                $this->temp_id = $hostCustomerInvoice;
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $hostPaymentRequest :
                $this->temp_id = $hostPaymentRequest;
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $adminPaidToHost :
                $this->temp_id = $adminPaidToHost;
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $hostAcknowledge :
                $this->temp_id = $hostAcknowledge;
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $customerOrderStatus :
                $this->temp_id = $customerOrderStatus;
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;

            default :
               
        }
        /**
         * To generate template
         */
        $this->inlineTranslation->suspend ();
        $this->generateTemplate ( $emailTemplateVariables, $senderInfo, $receiverInfo, $templateId, $area,$ccEmail);
        /**
         * Create object for transportbuilder
         */
        $transport = $this->_transportBuilder->getTransport ();
        /**
         * Send message function
         */
        $transport->sendMessage ();
        /**
         * Resume the inline translation
         */
        $this->inlineTranslation->resume ();
    }

    /**
     * Send custom mail
     *
     * @param Mixed $emailTemplateVariables            
     * @param Mixed $senderInfo            
     * @param Mixed $receiverInfo            
     * @return void
     */
    public function contacthostMailSendMethod($emailTemplateVariables, $senderInfo, $receiverInfo, $templateId,$ccEmail='') {
        /**
         * To declare the contacthost email template id
         */        
        $contactHost = 'airhotels_contact_host';
        $contactHostReply = 'airhotels_contact_host_reply';

        switch ($templateId) {
             case $contactHost :
                $this->temp_id = $contactHost;
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $contactHostReply :
                $this->temp_id = $contactHostReply;
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            default:
        }
        /**
         * To generate template
         */
        $this->inlineTranslation->suspend ();
        $this->generateTemplate ( $emailTemplateVariables, $senderInfo, $receiverInfo, $templateId, $area,$ccEmail);
        /**
         * Create an object for transportbuilder
         */
        $transport = $this->_transportBuilder->getTransport ();
        /**
         * Send message function
         */
        $transport->sendMessage ();
        /**
         * Resume the inline translation
         */
        $this->inlineTranslation->resume ();
    }
}