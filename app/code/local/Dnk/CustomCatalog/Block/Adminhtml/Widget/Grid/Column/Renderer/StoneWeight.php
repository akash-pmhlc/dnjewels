<?php

class Dnk_CustomCatalog_Block_Adminhtml_Widget_Grid_Column_Renderer_StoneWeight extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        $stoneId = $row->getStoneId();
		$rates = $this->getColumn()->getParam();
		if ($stoneId != '' && $rates != '') {
			$productRatesArray = explode(',',$rates);
			foreach ($productRatesArray as $productRateArray) {
				$productRate = explode('-',$productRateArray);
				if($productRate[0] == $row->getStoneId())
					$value = $productRate[2];
			}
			$html = '<input type="text" value="'.$value.'" name="stone-weight" class="input-text validate-number" disabled="">';
		} else {
			$html = '<input type="text" value="" name="stone-weight" class="input-text validate-number" disabled="" >';
		}
		return $html;
    }

}