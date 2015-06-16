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
							->addAttributeToSelect(array('goldrate14k','goldrate18k','entity_id','goldweight'))
							->addAttributeToFilter(
								array(
									array('attribute'=> 'goldrate14k','neq' => ''),
									array('attribute'=> 'goldrate18k','neq' => ''),
								)
							)
							->addAttributeToFilter('goldweight', array('neq' => ''))
							->load();
			
			foreach($products as $product) {
				
				$product = Mage::getModel('catalog/product')->load($product->getEntityId());
				
				if($product->getGoldrate14k() > 0) {
					$product->setGoldrate14k($product->getGoldweight()*$newGold24KRate*$newGold14KRate);
					$updateProductOptions[$product->getEntityId()]['14K'] = $product->getGoldrate14k();
				}
				if($product->getGoldrate18k() > 0) {
					$product->setGoldrate18k($product->getGoldweight()*$newGold24KRate*$newGold18KRate);
					$updateProductOptions[$product->getEntityId()]['18K'] = $product->getGoldrate18k();
				}
				
				$productOptions = $product->getOptions();
				foreach ($productOptions as $productOption) {
					if($productOption->getTitle() == 'Gold Purity') {
						$count = 0;
						foreach ($productOption->getValues() as $productOptionValue) {
							$optionId = $productOptionValue->getOptionId();
							$optionTypeIds[$count] = $productOptionValue->getOptionTypeId();
							$optionTypeTitle[$count] = $productOptionValue->getTitle();
							if($productOptionValue->getTitle() == '14 K')
								$optionTypeRate[$count] = $updateProductOptions[$product->getEntityId()]['14K'];
							if($productOptionValue->getTitle() == '18 K')
								$optionTypeRate[$count] = $updateProductOptions[$product->getEntityId()]['18K'];
							$count++;
						}	
					}
				}
		
				if($optionId != '' && !is_null($optionTypeIds)) {
					$count = 0;
					foreach($optionTypeIds as $optionTypeId) {
						$optionData = array(
									'is_delete'         => 0,
									'is_require'        => true,
									'previous_group'    => '',
									'title'             => 'Gold Purity',
									'type'              => 'radio',
									'sort_order'        => 1,
									'option_id'			=> $optionId,
									'values'            => array(
															array(
																'is_delete'     => 0,
																'title'         => $optionTypeTitle[$count],
																'price_type'    => 'fixed',
																'price'         => $optionTypeRate[$count],
																'option_type_id'=> $optionTypeIds[$count],
															)
														)
								);
					
						$product->setOptions(array($optionData));
						$product->setProductOptions(array($optionData));

						$opt = Mage::getSingleton('catalog/product_option');
						$opt->setProduct($product);
						$opt->addOption($optionData);
						$opt->saveOptions();
						$product->setOption($opt);
						$count++;
					}
				}
				
				$product->save();
			}
			
		}
		
		if(($newGold14KRate != $oldGold14KRate) && ($newGold24KRate == $oldGold24KRate)) {
			
			$products = Mage::getModel('catalog/product')->getCollection()
							->addAttributeToSelect(array('goldrate14k','entity_id','goldweight'))
							->addAttributeToFilter('goldrate14k', array('neq' => ''))
							->addAttributeToFilter('goldweight', array('neq' => ''))
							->load();
			
			foreach($products as $product) {
				if($product->getGoldrate14k() > 0) {
					$product = Mage::getModel('catalog/product')->load($product->getEntityId());
					$product->setGoldrate14k($product->getGoldweight()*$newGold24KRate*$newGold14KRate);
					$updateProductOptions[$product->getEntityId()]['14K'] = $product->getGoldrate14k();
				
					$productOptions = $product->getOptions();
					foreach ($productOptions as $productOption) {
						if($productOption->getTitle() == 'Gold Purity') {
							$count = 0;
							foreach ($productOption->getValues() as $productOptionValue) {
								if($productOptionValue->getTitle() == '14 K') {
									$optionTypeRate[$count] = $updateProductOptions[$product->getEntityId()]['14K'];
									$optionId = $productOptionValue->getOptionId();
									$optionTypeIds[$count] = $productOptionValue->getOptionTypeId();
									$optionTypeTitle[$count] = $productOptionValue->getTitle();
								}
								$count++;
							}	
						}
					}
		
					if($optionId != '' && !is_null($optionTypeIds)) {
						$count = 0;
						foreach($optionTypeIds as $optionTypeId) {
							$optionData = array(
									'is_delete'         => 0,
									'is_require'        => true,
									'previous_group'    => '',
									'title'             => 'Gold Purity',
									'type'              => 'radio',
									'sort_order'        => 1,
									'option_id'			=> $optionId,
									'values'            => array(
															array(
																'is_delete'     => 0,
																'title'         => $optionTypeTitle[$count],
																'price_type'    => 'fixed',
																'price'         => $optionTypeRate[$count],
																'option_type_id'=> $optionTypeIds[$count],
															)
														)
								);
					
							$product->setOptions(array($optionData));
							$product->setProductOptions(array($optionData));

							$opt = Mage::getSingleton('catalog/product_option');
							$opt->setProduct($product);
							$opt->addOption($optionData);
							$opt->saveOptions();
							$product->setOption($opt);
							$count++;
						}
					}
					
					$product->save();
				}
			}
			
		}
		
		if(($newGold18KRate != $oldGold18KRate) && ($newGold24KRate == $oldGold24KRate)) {
			
			$products = Mage::getModel('catalog/product')->getCollection()
							->addAttributeToSelect(array('goldrate18k','entity_id','goldweight'))
							->addAttributeToFilter('goldweight', array('neq' => ''))
							->load();
			
			foreach($products as $product) {
				if($product->getGoldrate18k() > 0) {
					$product = Mage::getModel('catalog/product')->load($product->getEntityId());
					$product->setGoldrate18k($product->getGoldweight()*$newGold24KRate*$newGold18KRate);
					$updateProductOptions[$product->getEntityId()]['18K'] = $product->getGoldrate18k();
				
					$productOptions = $product->getOptions();
					foreach ($productOptions as $productOption) {
						if($productOption->getTitle() == 'Gold Purity') {
							$count = 0;
							foreach ($productOption->getValues() as $productOptionValue) {
								if($productOptionValue->getTitle() == '18 K') {
									$optionTypeRate[$count] = $updateProductOptions[$product->getEntityId()]['18K'];
									$optionId = $productOptionValue->getOptionId();
									$optionTypeIds[$count] = $productOptionValue->getOptionTypeId();
									$optionTypeTitle[$count] = $productOptionValue->getTitle();
								}
								$count++;
							}	
						}
					}
		
					if($optionId != '' && !is_null($optionTypeIds)) {
						$count = 0;
						foreach($optionTypeIds as $optionTypeId) {
							$optionData = array(
									'is_delete'         => 0,
									'is_require'        => true,
									'previous_group'    => '',
									'title'             => 'Gold Purity',
									'type'              => 'radio',
									'sort_order'        => 1,
									'option_id'			=> $optionId,
									'values'            => array(
															array(
																'is_delete'     => 0,
																'title'         => $optionTypeTitle[$count],
																'price_type'    => 'fixed',
																'price'         => $optionTypeRate[$count],
																'option_type_id'=> $optionTypeIds[$count],
															)
														)
								);
					
							$product->setOptions(array($optionData));
							$product->setProductOptions(array($optionData));

							$opt = Mage::getSingleton('catalog/product_option');
							$opt->setProduct($product);
							$opt->addOption($optionData);
							$opt->saveOptions();
							$product->setOption($opt);
							$count++;
						}
					}
					
					$product->save();
				}
			}
		}
		
		/*foreach($updateProductOptions as $key=>$value) {
		
			$product = Mage::getModel('catalog/product')->load($key);
			$productOptions = $product->getOptions();
			foreach ($productOptions as $productOption) {
				if($productOption->getTitle() == 'Gold Purity') {
					$count = 0;
					foreach ($productOption->getValues() as $productOptionValue) {
						$optionId = $productOptionValue->getOptionId();
						$optionTypeIds[$count] = $productOptionValue->getOptionTypeId();
						$optionTypeTitle[$count] = $productOptionValue->getTitle();
						if($productOptionValue->getTitle() == '14 K')
							$optionTypeRate[$count] = $value['14K'];
						if($productOptionValue->getTitle() == '18 K')
							$optionTypeRate[$count] = $value['18K'];
						$count++;
					}	
				}
			}
		
			if($optionId != '' && !is_null($optionTypeIds)) {
				$count = 0;
				foreach($optionTypeIds as $optionTypeId) {
					$optionData = array(
									'is_delete'         => 0,
									'is_require'        => true,
									'previous_group'    => '',
									'title'             => 'Gold Purity',
									'type'              => 'radio',
									'sort_order'        => 1,
									'option_id'			=> $optionId,
									'values'            => array(
															array(
																'is_delete'     => 0,
																'title'         => $optionTypeTitle[$count],
																'price_type'    => 'fixed',
																'price'         => $optionTypeRate[$count],
																'option_type_id'=> $optionTypeIds[$count],
															)
														)
								);
					$product->setOptions(array($optionData));
					$product->setProductOptions(array($optionData));

					$opt = Mage::getSingleton('catalog/product_option');
					$opt->setProduct($product);
					$opt->addOption($optionData);
					$opt->saveOptions();
					$product->setOption($opt);
					$product->save();
					$count++;
				}
			}
		}*/
		
		Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Product prices were successfully updated"));
		
	}
 
 
}