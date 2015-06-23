<?php
 
class Dnk_CustomCatalog_Block_Adminhtml_Catalog_Product_Edit_Stonetab
extends Mage_Adminhtml_Block_Widget
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function canShowTab() 
    {
        return true;
    }
 
    public function getTabLabel() 
    {
        return $this->__('Stone and Rates');
    }
 
    public function getTabTitle()        
    {
        return $this->__('Stone and Rates');
    }
 
    public function isHidden()
    {
        return false;
    }
 
    public function getTabUrl() 
    {
        return $this->getUrl('*/*/stonerates', array('_current' => true));
    }
 
    public function getTabClass()
    {
        return 'ajax';
    }
 
}