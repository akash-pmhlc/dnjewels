<?php
class Dnk_Sms_Block_Sms extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getSms()     
     { 
        if (!$this->hasData('sms')) {
            $this->setData('sms', Mage::registry('sms'));
        }
        return $this->getData('sms');
        
    }
}