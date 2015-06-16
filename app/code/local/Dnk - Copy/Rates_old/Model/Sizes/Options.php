<?php

class Dnk_Rates_Model_Sizes_Options extends Mage_Core_Model_Abstract
{
    public function getOptionArray(){

		$sizes = Mage::getModel('sizes/sizes')->getCollection()->getData();
	   
		$sizeArray = array();
		foreach($sizes as $size) {
			$sizeArray[$size['size_id']] = $size['size'];	   
		}
		
		return $sizeArray;
    }

}
	 