<?php
 
class Dnk_Stone_Model_Observer extends Varien_Object
{
    public function changeInGoldRates($observer)
    {
		$newGold24KRate = Mage::getStoreConfig('gold/goldrate/rate24k');
		$newGold14KRate = Mage::getStoreConfig('gold/goldrate/rate14k');
		$newGold18KRate = Mage::getStoreConfig('gold/goldrate/rate18k');

        $oldGold24KRate = Mage::getSingleton('adminhtml/session')->getOldGold24KRate();
        $oldGold14KRate = Mage::getSingleton('adminhtml/session')->getOldGold18KRate();
        $oldGold18KRate = Mage::getSingleton('adminhtml/session')->getOldGold14KRate();

        Mage::getSingleton('adminhtml/session')->unsOldGold24KRate();
        Mage::getSingleton('adminhtml/session')->unsOldGold18KRate();
        Mage::getSingleton('adminhtml/session')->unsOldGold14KRate();

		if(($newGold24KRate != $oldGold24KRate)) {
			
			$products = Mage::getModel('catalog/product')->getCollection()
							->addAttributeToFilter(
								array(
									array('attribute'=> 'goldrate14k','neq' => ''),
									array('attribute'=> 'goldrate18k','neq' => ''),
								)
							)
							->addAttributeToFilter('goldweight', array('neq' => ''))
							->load();
			
			
			foreach($products as $product) {
				if ($product->getOptions()) {
					if($product->getGoldrate14k() > 0) {
						$listingPrice = $listingPrice-$product->getGoldrate14k();
						$product->setGoldrate14k($product->getGoldweight()*$newGold24KRate*$newGold14KRate/10);
						$listingPrice = $listingPrice+$product->getGoldrate14k();
					}
					
					if($product->getGoldrate18k() > 0) {
						if (!isset($listingPrice)) {
							$listingPrice = $listingPrice-$product->getGoldrate18k();
						}
						$product->setGoldrate18k($product->getGoldweight()*$newGold24KRate*$newGold18KRate/10);
						if (!isset($listingPrice)) {
							$listingPrice = $listingPrice+$product->getGoldrate18k();
						}
					}

					$taxRate = Mage::getStoreConfig('dnktax/taxrate/rate');
					if($taxRate > 0) $product->setTaxrate(($listingPrice-$product->getTaxrate())*$taxRate/100);
					
					if($listingPrice > 0) $product->setListingprice(round($listingPrice+$product->getTaxrate()));
								
					$all_options = $product->getOptions();
					$productOptions = array();
					if($all_options) {
				    	foreach ($all_options as  $option) {
					   		$productOptions[$option['option_id']]['option_id'] = $option['option_id'];
					   		$productOptions[$option['option_id']]['is_delete'] = '';
					   		$productOptions[$option['option_id']]['previous_type'] = $option['type'];
					   		$productOptions[$option['option_id']]['previous_group'] = 'select';
					   		$productOptions[$option['option_id']]['id'] = $option['option_id'];
					   		$productOptions[$option['option_id']]['title'] = $option['title'];
					   		$productOptions[$option['option_id']]['type'] = $option['type'];
					   		$productOptions[$option['option_id']]['is_require'] = $option['is_require'];
					   		$productOptions[$option['option_id']]['sort_order'] = $option['sort_order'];
				   		   
					       	foreach ($option->getValues() as  $value) {
						       	$productOptions[$option['option_id']]['values'][$value['option_type_id']]['option_type_id'] = $value['option_type_id'];
						       	$productOptions[$option['option_id']]['values'][$value['option_type_id']]['is_delete'] = '';
						       	$productOptions[$option['option_id']]['values'][$value['option_type_id']]['title'] = $value['title'];
						       	$productOptions[$option['option_id']]['values'][$value['option_type_id']]['price'] = $value['price'];
						       	$productOptions[$option['option_id']]['values'][$value['option_type_id']]['price_type'] = $value['price_type'];
						       	$productOptions[$option['option_id']]['values'][$value['option_type_id']]['sku'] = $value['sku'];
						       	$productOptions[$option['option_id']]['values'][$value['option_type_id']]['sort_order'] = $value['sort_order'];       	
					       	}
				    	}
					}

					$postProductOptions = $productOptions;
					foreach ($postProductOptions as $parentKey => $postProductOption) {

						if($postProductOption['title'] == 'Gold Purity') {

							foreach ($postProductOption['values'] as $childKey => $postProductOptionValue) {
								if($postProductOptionValue['title'] == '14K') {
									$productOptions[$parentKey]['values'][$childKey]['price'] = $product->getGoldrate14k();
								}
								if($postProductOptionValue['title'] == '18K') {
									$productOptions[$parentKey]['values'][$childKey]['price'] = $product->getGoldrate18k();
								}
							}

							break;
						}
					}

					$product->setData('can_save_custom_options',1);
					$product->setProductOptions($productOptions);
					$product->save();
				}
				
				
			}
			
		}
		
		Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Product prices were successfully updated"));
		
	}
 
 
}