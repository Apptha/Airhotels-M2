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
 /**
 * Clas contains banner save form functions
**/
namespace Apptha\Airhotels\Controller\Adminhtml\Uploadvideo;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $upload;
    /**
    * CustomClass constructor.
    *
    * @param \Magento\Framework\App\Action\Context               $context
    * @param \Custom\Module\Controller\Adminhtml\UpdateData\Save $save
    */
    public function __construct(
            \Magento\Backend\App\Action\Context $context,
        \Apptha\Airhotels\Controller\Adminhtml\Cities\Save $upload
        ) {
            parent::__construct($context);
            $this->upload = $upload;
    }
public function execute()
    {
        $data = $this->getRequest()->getParams();
        if ($data) {
            $model = $this->_objectManager->create('Apptha\Airhotels\Model\Uploadvideo');
             if(isset($_FILES['image_url']['name']) && $_FILES['image_url']['name'] != '' && $_FILES['image_url']['size'] >= 2048) {
try {
                       $fileId = 'image_url';
                       $absolutePath = 'Airhotels'. DIRECTORY_SEPARATOR .'Banner';
                       $logoName = $this->uploads->uploadCityImage ( $fileId, $absolutePath );
                       if(!empty($logoName)){
                            $data['image_url'] = $logoName;
                        }else{
                            unset($data['image_url']);
                        }
} catch (Exception $e) {
$data['image_url'] =$_FILES['image_url']['name'];
}
}
else{
$data['image_url'] = $data['image_url']['value'];
}
$id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Frist Grid Has been Saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
            }
            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('banner_id' => $this->getRequest()->getParam('banner_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
}