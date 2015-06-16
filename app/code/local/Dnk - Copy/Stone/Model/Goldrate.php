<?php

class Dnk_Stone_Model_Goldrate extends Mage_Core_Model_Config_Data
{
    public function save()
    {
        $gold24KRate = Mage::getStoreConfig('gold/goldrate/rate24k');
		$gold14KRate = Mage::getStoreConfig('gold/goldrate/rate14k');
		$gold18KRate = Mage::getStoreConfig('gold/goldrate/rate18k');
        Mage::getSingleton('adminhtml/session')->setOldGold24KRate($gold24KRate);
        Mage::getSingleton('adminhtml/session')->setOldGold18KRate($gold18KRate);
        Mage::getSingleton('adminhtml/session')->setOldGold14KRate($gold14KRate);
 
        return parent::save();  
    }

}
	 