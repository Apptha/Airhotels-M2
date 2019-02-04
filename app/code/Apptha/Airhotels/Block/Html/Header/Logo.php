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
namespace Apptha\Airhotels\Block\Html\Header;
class Logo extends \Magento\Theme\Block\Html\Header\Logo
{
    /**
     * Current template name
     *
     * @var string
     */
    protected $_template = 'Magento_Theme::html/header/logo.phtml';

    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $_fileStorageHelper;
    protected $_request;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageHelper
     * @param array $data
     */
   public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageHelper,
    array $data = []
)
{

    parent::__construct($context, $fileStorageHelper,$data);
}

    /**
     * Check if current url is url for home page
     *
     * @return bool
     */
    public function isHomePage()
    {
        $currentUrl = $this->getUrl('', ['_current' => true]);
        $urlRewrite = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        return $currentUrl == $urlRewrite;
    }
    /**
     * Get logo image URL
     *
     * @return string
     */
    public function getLogoSrc()
    {
        if (empty($this->_data['logo_src'])) {
            $this->_data['logo_src'] = $this->_getLogoUrl();
        }
        return $this->_data['logo_src'];
    }
    /**
     * Retrieve logo image URL
     *
     * @return string
     */
    protected function _getLogoUrl()
    {
        $folderName = \Magento\Config\Model\Config\Backend\Image\Logo::UPLOAD_DIR;
        $storeLogoPath = $this->_scopeConfig->getValue(
            'design/header/logo_src',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $path = $folderName . '/' . $storeLogoPath;
        $logoUrl = $this->_urlBuilder
                ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;

        if ($storeLogoPath !== null && $this->_isFile($path)) {
            $url = $logoUrl;
        } elseif ($this->getLogoFile()) {
            $url = $this->getViewFileUrl($this->getLogoFile());
        } else {
            $url = $this->getViewFileUrl('images/logo.svg');
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
        $homePage = $this->isHomePage();
        if ($request->getFullActionName() == 'cms_index_index') {
            return $url;
        }
        else{
            if($homePage =='1'){
                return  $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'Airhotels/Logo/logo.png';
            } else{
                return $this->getViewFileUrl('Apptha_Airhotels::images/logo/inner-logo.png');
            }
        }

    }
    /**
     * Retrieve logo width
     *
     * @return int
     */
    public function getLogoWidth()
    {
        if (empty($this->_data['logo_width'])) {
            $this->_data['logo_width'] = $this->_scopeConfig->getValue(
                'design/header/logo_width',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
        if ($request->getFullActionName() == 'cms_index_index') {
            return (int)$this->_data['logo_width'] ? : (int)$this->getLogoImgWidth();
        }else{
            return $this->_data['logo_width'] ='auto';
        }

    }

    /**
     * Retrieve logo height
     *
     * @return int
     */
    public function getLogoHeight()
    {
        if (empty($this->_data['logo_height'])) {
            $this->_data['logo_height'] = $this->_scopeConfig->getValue(
                'design/header/logo_height',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
        if ($request->getFullActionName() == 'cms_index_index') {
        return (int)$this->_data['logo_height'] ? : (int)$this->getLogoImgHeight();
        }else{
            return $this->_data['logo_height'] ='auto';
        }
    }

}

