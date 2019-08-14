<?php

/**
 * Description of GoogleTrustedStoresController
 * @package   CueBlocks_GoogleTrustedStores
 * @company   CueBlocks - http://www.cueblocks.com/
 * @author    Francesco Magazzu' <francesco.magazzu at cueblocks.com>
 */
class Inavante_GoogleTrustedStores_Adminhtml_GoogletrustedstoresController extends Mage_Adminhtml_Controller_action {

    /**
     * Init actions
     *
     * @return Mage_Adminhtml_SitemapController
     */
    protected function _initAction() {

        // load layout, set active menu and breadcrumbs
        $this->loadLayout();

//        $this->_addBreadcrumb(Mage::helper('catalog')->__('Catalog'), Mage::helper('catalog')->__('Catalog'));
//        $this->_addBreadcrumb(Mage::helper('sitemapEnhanced')->__('Sitemap Enhanced'), Mage::helper('sitemapEnhanced')->__('Sitemap Enhanced'));
        $this->_setActiveMenu('reports/sales');
        return $this;
    }

    /**
     * Ajax action for billing agreements
     *
     */
    public function gridAction() {
        $this->loadLayout(false)
                ->renderLayout();
    }

    /**
     * Index action
     */
    public function indexAction() {

        $this->_title($this->__('Catalog'))->_title($this->__('Google Trusted Stores Feed Log'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_initAction();
        $this->renderLayout();
    }
    
    /**
     * Index action
     */
    public function shiplogAction() {

        $this->_title($this->__('Catalog'))->_title($this->__('Google Trusted Stores Feed Log'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_initAction();
        $this->renderLayout();
    }
    /**
     * Index action
     */
    public function cancellogAction() {

        $this->_title($this->__('Catalog'))->_title($this->__('Google Trusted Stores Feed Log'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
//    protected function _isAllowed() {
//        return Mage::getSingleton('admin/session')->isAllowed('googletrustedstores/googletrustedstores');
//    }

}
