<?php

class Dnk_Rates_Model_Shapes_Options extends Mage_Core_Model_Abstract
{
    public function getOptionArray(){

		$shapes = Mage::getModel('shapes/shapes')->getCollection()->getData();
		
		$shapeArray = array();
		foreach($shapes as $shape) {
			$shapeArray[$shape['shape_id']] = $shape['shape'];	   
		}
		
		return $shapeArray;
    }

}
	 