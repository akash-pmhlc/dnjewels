<?php
 
class Dnk_CustomCatalog_Block_Adminhtml_Catalog_Product_Edit_Tab
extends Mage_Adminhtml_Block_Widget
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function canShowTab() 
    {
        return true;
    }
 
    public function getTabLabel() 
    {
        return $this->__('No. of Diamonds and Rates');
    }
 
    public function getTabTitle()        
    {
        return $this->__('No. of Diamonds and Rates');
    }
 
    public function isHidden()
    {
        return false;
    }
 
    public function getTabUrl() 
    {
        return $this->getUrl('*/*/diamondrates', array('_current' => true));
    }
 
    public function getTabClass()
    {
        return 'ajax';
    }
 
}