<?php

class Dnk_CustomCatalog_Block_Adminhtml_Widget_Grid_Column_Renderer_Weight extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        $rateId = $row->getRateIdChild();
		$rates = $this->getColumn()->getParam();

		if ($rateId != '' && $rates != '') {
			$productRatesArray = explode(',',$rates);
			foreach ($productRatesArray as $productRateArray) {
				$productCertificatesRate = explode('|',$productRateArray);
				foreach ($productCertificatesRate as $productCertificateRate) {
					$productRate = explode('-',$productCertificateRate);
					if($productRate[0] == $row->getRateIdChild())
						$value = $productRate[2];
				}
			}
			$html = '<input type="text" value="'.$value.'" name="diamond-weight" class="input-text validate-number" disabled="">';
		} else {
			$html = '<input type="text" value="" name="diamond-weight" class="input-text validate-number" disabled="">';
		}
		return $html;
    }

}