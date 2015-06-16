<?php
 
require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'Catalog'.DS.'ProductController.php');
 
class Dnk_CustomCatalog_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    /**
     * Get custom products grid and serializer block
     */
    public function diamondratesAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.diamondrates')
            ->setRates($this->getRequest()->getPost('rates', null));
        $this->renderLayout();
    }
 
    /**
     * Get custom products grid
     */
    public function diamondratesGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.diamondrates')
            ->setRates($this->getRequest()->getPost('rates', null));
        $this->renderLayout();
    }
	
    /**
     * Get custom products grid and serializer block
     */
    public function stoneratesAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.stonerates')
            ->setRates($this->getRequest()->getPost('rates', null));
        $this->renderLayout();
    }
 
    /**
     * Get custom products grid
     */
    public function stoneratesGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.stonerates')
            ->setRates($this->getRequest()->getPost('rates', null));
        $this->renderLayout();
    }
 
}