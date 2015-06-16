<?php
class Dnk_CustomCatalog_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getSelectedRates($productRates) {
		
		$productRatesArray = explode(',',$productRates);
		
		foreach ($productRatesArray as $productRateArray) {
			$productRate = explode('-',$productRateArray);
			$rateIdArray[] = $productRate[0];
		}
		
		return $rateIdArray;
	}
	
	public function getSelectedStoneRates($productRates) {
		
		$productRatesArray = explode(',',$productRates);
		
		foreach ($productRatesArray as $productRateArray) {
			$productRate = explode('-',$productRateArray);
			$rateIdArray[] = $productRate[0];
		}
		
		return $rateIdArray;
	}
}
	 