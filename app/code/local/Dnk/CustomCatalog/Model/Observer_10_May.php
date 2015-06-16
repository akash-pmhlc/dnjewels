<?php
 
class Dnk_CustomCatalog_Model_Observer extends Varien_Object
{
    public function catalogProductPrepareSave($observer)
    {
        $event = $observer->getEvent();
 
        $product = $event->getProduct();
        $request = $event->getRequest();
		$postProduct = $request->getPost('product');
		$postProductOptions = $postProduct['options'];
        $diamondrates = $request->getPost('diamondrates');

        if (isset($diamondrates['rates']) && !$product->getCustomReadonly()) {
            $rateData = Mage::helper('adminhtml/js')->decodeGridSerializedInput($diamondrates['rates']);
			$rateAttributeData = '';
			$rateIdsAttributeData = '';

			$certificatesModel = Mage::getModel("certificates/certificates")->getCollection()->getData();
			foreach ($certificatesModel as $certificate) {
				$certificateArray[$certificate['certificate_id']] = $certificate['certificate'];
			}
				
			foreach ($rateData as $key=>$valueData) {

				$childRatemodel = Mage::getModel("rates/childrates")->load($key);
				$size = $childRatemodel->getSizeChild();
				$shape = $childRatemodel->getShapeChild();

				$ratemodel = Mage::getModel("rates/rates")->getCollection()->addFieldToFilter('shape',$shape)->addFieldToFilter('size',$size)->getData();
				foreach ($ratemodel as $rate) {
					$rateAttributeData .= $rate['rate_id'].'-';
					$rateIdsAttributeData .= $rate['rate_id'].'-'.$certificateArray[$rate['certificate']].'-';
					foreach ($valueData as $value) {
						$rateAttributeData .= $value.'-';
						$diamondWeight = $value;
					}
					$calculatedPrice = $diamondWeight*$rate['rate'];
					$rateIdsAttributeData .= $calculatedPrice.'|';
					$rateAttributeData = rtrim($rateAttributeData,"-");
					$rateAttributeData .= '|';
				}
				$rateAttributeData = rtrim($rateAttributeData,"|");
				$rateAttributeData .= ',';
				$rateIdsAttributeData = rtrim($rateIdsAttributeData,"|");
				$rateIdsAttributeData .= ',';
			}
			
			$rateAttributeData = rtrim($rateAttributeData,",");
			$product->setRates($rateAttributeData);
			$rateIdsAttributeData = rtrim($rateIdsAttributeData,",");
			$product->setRateids($rateIdsAttributeData);
        }
		
        $stonerates = $request->getPost('stonerates');
		
        if (isset($stonerates['rates']) && !$product->getCustomReadonly()) {
            $rateData = Mage::helper('adminhtml/js')->decodeGridSerializedInput($stonerates['rates']);
			$rateAttributeData = '';
			$rateIdsAttributeData = '';
			foreach ($rateData as $key=>$valueData) {
				$rateAttributeData .= $key.'-';
				$rateIdsAttributeData .= $key.',';
				foreach ($valueData as $value) {
					$rateAttributeData .= $value.'-';
				}
				$rateAttributeData = rtrim($rateAttributeData,"-");
				$rateAttributeData .= ',';
			}
			$rateAttributeData = rtrim($rateAttributeData,",");
			$product->setStonerates($rateAttributeData);
			$rateIdsAttributeData = rtrim($rateIdsAttributeData,",");
			$product->setStoneids($rateIdsAttributeData);
        }
		
		$totalWeight = 0;
		$price = 0;
		
		if($product->getRates() != '') {

			$productRatesArray = explode(',',$product->getRates());
			foreach ($productRatesArray as $productRateArray) {
				$productRate = explode('-',$productRateArray);
				$productWeight = explode('|', $productRate[2]);
				$totalWeight += $productWeight[0];
			}

			$diamondRateIdsArray = explode(',',$product->getRateids());
			foreach ($diamondRateIdsArray as $diamondRateIdArray) {

				$diamondRateIdCertificateArray = explode('|', $diamondRateIdArray);
				
				foreach ($diamondRateIdCertificateArray as $diamondRateIdCertificateRate) {
					$certificateWiseRate = explode('-',$diamondRateIdCertificateRate);
					$pricePerCertificate[$certificateWiseRate[1]] += $certificateWiseRate[2];
				}

			}

			$product->setDiamondcharge(min($pricePerCertificate));
			$price = $product->getDiamondcharge();
			$certificateSelected = array_search ($price, $pricePerCertificate);
		}
		
		if($product->getStonerates() != '') {
			$productRatesArray = explode(',',$product->getStonerates());
			foreach ($productRatesArray as $productRateArray) {
				$productRate = explode('-',$productRateArray);
				$rateId = $productRate[0];
				$model = Mage::getModel("stone/stones")->load($rateId);
				$rate = $model->getRate();
				$totalWeight += $productRate[1];
				$price += $rate*$productRate[1];
			}
			$product->setStonecharge($price-$product->getDiamondcharge());
		}
		
		
		$productOptions = $product->getProductOptions();
		foreach ($productOptions as $productOption) {
			
			if (($productOption['title'] != 'Gold Purity') && ($productOption['title'] != 'Diamond Color & Purity') && ($productOption['title'] != 'Gold Color')) {
				if($productOption['option_id'] != $productOption['id']) {
					$optionId = $productOption['id'];
				} else {
					$optionId = $productOption['option_id'];
				}

				$minPrice = 0;
				$option_id = 0;
				$option_type_id = 0;
				$goldWeightString = '';
				
				foreach ($productOption['values'] as $key=>$productOptionValue) {
					if ($key != $productOptionValue['option_type_id']) {
						$optionTypeId = $key;
					} else {
						$optionTypeId = $productOptionValue['option_type_id'];
					}
					$optionPrice = $postProductOptions[$optionId]['values'][$optionTypeId]['price'];
					$optionTitle = $postProductOptions[$optionId]['values'][$optionTypeId]['title'];
					if(!is_null($optionPrice)) {
						$goldWeightString .= $optionTitle.'-'.$optionPrice.',';
						if($minPrice == 0) {
							$minPrice = $optionPrice;
							$option_type_id = $optionTypeId;
						} elseif($optionPrice < $minPrice) {
							$minPrice = $optionPrice;
							$option_type_id = $optionTypeId;
						}
					}
				}
				$goldWeightString = rtrim($goldWeightString,",");
				$minVariableCharge = $minPrice;
				
				$model = Mage::getModel('customoption/customoption');
				$tablename=Mage::getModel('customoption/customoption')->getResource()->getTableName();
				$connection=Mage::getSingleton('core/resource')->getConnection('core_write');
				$fields=array();
				
				$option = Mage::helper('customoption')->getOption($optionId);
				if(!$option) {
					$model->setData('option_id',$optionId);
					$model->setData('option_type_id',$option_type_id);
					$model->save();
					$model->unsetData();
				} else {
					if($option[0]['option_type_id']!=$option_type_id) {
						$fields['option_type_id']=$option_type_id;
						$where=$connection->quoteInto('option_id=? ',$optionId);
						$connection->update($tablename, $fields, $where);
						$connection->commit();
					}
				}
			
			}
		}

		$product->setGoldweight($goldWeightString);
		
		$totalWeight = $minVariableCharge + ($totalWeight*0.2);
		
		$goldRate = Mage::getStoreConfig('gold/goldrate/rate24k');
		
		if($goldRate > 0) {
			$goldWeights = explode(',', $goldWeightString);
			foreach ($goldWeights as $goldWeight) {
				$goldArray = explode('-', $goldWeight);
				$goldRate14K = Mage::getStoreConfig('gold/goldrate/rate14k');
				$goldRate14kString .= ($goldArray[1]*$goldRate*$goldRate14K/10).'-'.$goldArray[0].',';
				$goldRate18K = Mage::getStoreConfig('gold/goldrate/rate18k');
				$goldRate18kString .= ($goldArray[1]*$goldRate*$goldRate18K/10).'-'.$goldArray[0].',';
			}
			
			$goldRate14kString = rtrim($goldRate14kString,',');
			$product->setGoldrate14k($goldRate14kString);
			
			$goldRate18kString = rtrim($goldRate18kString,',');
			$product->setGoldrate18k($goldRate18kString);
			
			if($product->getGoldrate14k() != '') {
				$goldSelected = '14K';
			} elseif ($product->getGoldrate18k() != '') {
				$goldSelected = '18K';
			}
		}

		if($totalWeight > 0) $product->setTotalweight($totalWeight);
		
		if($goldSelected == '14K') {
			$goldRate14kArray = explode(',', $product->getGoldrate14k());
			$listingPriceArray14K = explode('-', $goldRate14kArray[0]);
			$goldRate18kArray = explode(',', $product->getGoldrate18k());
			$listingPriceArray18K = explode('-', $goldRate18kArray[0]);
			$listingPrice = $price + $listingPriceArray14K[0];
		} else {
			$goldRate18kArray = explode(',', $product->getGoldrate18k());
			$listingPriceArray18K = explode('-', $goldRate18kArray[0]);
			$listingPrice = $price + $listingPriceArray18K[0];
		}

		foreach ($productOptions as $productOption) {
			
			if($productOption['title'] == 'Gold Purity') {
				$count = 'goldpurity';
				if($productOption['option_id'] != $productOption['id']) {
					$optionId = $productOption['id'];
				} else {
					$optionId = $productOption['option_id'];
				}
				$parentOptionArray[$count]['is_delete'] = $postProductOptions[$optionId]['is_delete'];
				$parentOptionArray[$count]['title'] = $postProductOptions[$optionId]['title'];
				$parentOptionArray[$count]['type'] = $postProductOptions[$optionId]['type'];
				$parentOptionArray[$count]['sort_order'] = $postProductOptions[$optionId]['sort_order'];
				$parentOptionArray[$count]['option_id'] = $postProductOptions[$optionId]['option_id'];
				$parentOptionArray[$count]['id'] = $postProductOptions[$optionId]['id'];

				foreach ($productOption['values'] as $key => $productOptionValue) {
					if ($key != $productOptionValue['option_type_id']) {
						$optionTypeId = $key;
						$optionTypeIdAssigned = '-1';
					} else {
						$optionTypeId = $productOptionValue['option_type_id'];
						$optionTypeIdAssigned = $optionTypeId;
					}
					$optionTypesArray[$count][$optionTypeId]['is_delete'] = $postProductOptions[$optionId]['values'][$optionTypeId]['is_delete'];
					$optionTypesArray[$count][$optionTypeId]['title'] = $productOptionValue['title'];
					$optionTypesArray[$count][$optionTypeId]['sort_order'] = $postProductOptions[$optionId]['values'][$optionTypeId]['sort_order'];
					$optionTypesArray[$count][$optionTypeId]['price_type'] = 'fixed';
					$optionTypesArray[$count][$optionTypeId]['option_type_id'] = $optionTypeIdAssigned;
					
					if($productOptionValue['title'] == '14K') {
						//$optionTypesArray[$count][$optionTypeId]['price'] = $product->getGoldrate14k();
						//$goldRate14kArray = explode(',', $product->getGoldrate14k());
						$optionTypesArray[$count][$optionTypeId]['price'] = $listingPriceArray14K[0];
					}
					if($productOptionValue['title'] == '18K') {
						//$optionTypesArray[$count][$optionTypeId]['price'] = $product->getGoldrate18k();
						//$goldRate18kArray = explode(',', $product->getGoldrate18k());
						$optionTypesArray[$count][$optionTypeId]['price'] = $listingPriceArray18K[0];
					}
					
					if($productOptionValue['title'] == $goldSelected) {
						$option_id = $optionId;
						$option_type_id = $optionTypeId;
					
						$model = Mage::getModel('customoption/customoption');
						$tablename = Mage::getModel('customoption/customoption')->getResource()->getTableName();
						$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
						$fields=array();
						$option=Mage::helper('customoption')->getOption($option_id);
						if(!$option) {
							$model->setData('option_id',$option_id);
							$model->setData('option_type_id',$option_type_id);
							$model->save();
							$model->unsetData();
						} else {
							if($option[0]['option_type_id']!=$option_type_id) {
								$fields['option_type_id']=$option_type_id;
								$where=$connection->quoteInto('option_id=? ',$option_id);
								$connection->update($tablename, $fields, $where);
								$connection->commit();
							}
						}
					}
					
				}
				
			} elseif($productOption['title'] == 'Diamond Color & Purity') {
				$count = 'diamondpurity';
				if($productOption['option_id'] != $productOption['id']) {
					$optionId = $productOption['id'];
				} else {
					$optionId = $productOption['option_id'];
				}
				$parentOptionArray[$count]['is_delete'] = $postProductOptions[$optionId]['is_delete'];
				$parentOptionArray[$count]['title'] = $postProductOptions[$optionId]['title'];
				$parentOptionArray[$count]['type'] = $postProductOptions[$optionId]['type'];
				$parentOptionArray[$count]['sort_order'] = $postProductOptions[$optionId]['sort_order'];
				$parentOptionArray[$count]['option_id'] = $postProductOptions[$optionId]['option_id'];
				$parentOptionArray[$count]['id'] = $postProductOptions[$optionId]['id'];

				foreach ($productOption['values'] as $key => $productOptionValue) {
					if ($key != $productOptionValue['option_type_id']) {
						$optionTypeId = $key;
						$optionTypeIdAssigned = '-1';
					} else {
						$optionTypeId = $productOptionValue['option_type_id'];
						$optionTypeIdAssigned = $optionTypeId;
					}
					$optionTypesArray[$count][$optionTypeId]['is_delete'] = $postProductOptions[$optionId]['values'][$optionTypeId]['is_delete'];
					$optionTypesArray[$count][$optionTypeId]['title'] = $productOptionValue['title'];
					$optionTypesArray[$count][$optionTypeId]['sort_order'] = $postProductOptions[$optionId]['values'][$optionTypeId]['sort_order'];
					$optionTypesArray[$count][$optionTypeId]['price_type'] = 'fixed';
					$optionTypesArray[$count][$optionTypeId]['option_type_id'] = $optionTypeIdAssigned;
					
					$optionTypesArray[$count][$optionTypeId]['price'] = $pricePerCertificate[$productOptionValue['title']];

					if($productOptionValue['title'] == $certificateSelected) {
						$option_id = $optionId;
						$option_type_id = $optionTypeId;
						$model = Mage::getModel('customoption/customoption');
						$tablename = Mage::getModel('customoption/customoption')->getResource()->getTableName();
						$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
						$fields=array();
						$option=Mage::helper('customoption')->getOption($option_id);
						if(!$option) {
							$model->setData('option_id',$option_id);
							$model->setData('option_type_id',$option_type_id);
							$model->save();
							$model->unsetData();
						} else {
							if($option[0]['option_type_id']!=$option_type_id) {
								$fields['option_type_id']=$option_type_id;
								$where=$connection->quoteInto('option_id=? ',$option_id);
								$connection->update($tablename, $fields, $where);
								$connection->commit();
							}
						}
					}
					
				}
				
			} elseif ($productOption['title'] == 'Gold Color') {
				$color = 'Yellow';
				if($productOption['option_id'] != $productOption['id']) {
					$optionId = $productOption['id'];
				} else {
					$optionId = $productOption['option_id'];
				}
				$option_type_id = 0;
				foreach ($productOption['values'] as $key => $productOptionValue) {
					if ($key != $productOptionValue['option_type_id']) {
						$optionTypeId = $key;
					} else {
						$optionTypeId = $productOptionValue['option_type_id'];
					}
					if($productOptionValue['title'] == $color) {
						$option_id = $productOption['option_id'];
						$option_type_id = $optionTypeId;
					} else {
						$option_type_id_if_color_not_found = $optionTypeId;
					}
				}
				
				if($option_id == '') {
					$option_id = $optionId;
					$option_type_id = $option_type_id_if_color_not_found;
				}
				
				$model = Mage::getModel('customoption/customoption');
				$tablename=Mage::getModel('customoption/customoption')->getResource()->getTableName();
				$connection=Mage::getSingleton('core/resource')->getConnection('core_write');
				$fields=array();
				$option = Mage::helper('customoption')->getOption($option_id);
				if(!$option) {
					$model->setData('option_id',$option_id);
					$model->setData('option_type_id',$option_type_id);
					$model->save();
					$model->unsetData();
				} else {
					if($option[0]['option_type_id']!=$option_type_id) {
						$fields['option_type_id']=$option_type_id;
						$where=$connection->quoteInto('option_id=? ',$option_id);
						$connection->update($tablename, $fields, $where);
						$connection->commit();
					}
				}
				
			}
			
		}
		
		if(!is_null($parentOptionArray) && !is_null($optionTypesArray)) {
			
			foreach($parentOptionArray as $key => $parentOption) {
				
				$optionData = array(
								'is_delete'         => $parentOption['is_delete'],
								'is_require'        => true,
								'previous_group'    => '',
								'title'             => $parentOption['title'],
								'type'              => $parentOption['type'],
								'sort_order'        => $parentOption['sort_order'],
								'option_id'			=> $parentOption['option_id'],
								'id'				=> $parentOption['id'],
								'values'            => $optionTypesArray[$key]
							);
						
						$newOptionsArray = $product->getProductOptions();
						$newOptionsArray[$parentOption['id']] = $optionData;

						$product->setOptions($newOptionsArray);
						$product->setProductOptions($newOptionsArray);
						$opt = Mage::getSingleton('catalog/product_option');
						$opt->setProduct($product);
						$opt->addOption($optionData);
						$opt->saveOptions();
						$product->setOption($opt);
			}			
		}


		$listingPrice = $listingPrice + $product->getCertificatecharge();
		
		$listingPrice = $listingPrice + $minDiamondCertificatePrice;
		
		//$listingPrice = $listingPrice + $minVariableCharge;
		
		//if($minVariableCharge > 0) $product->setVariablecharge($minVariableCharge);
		
		if($totalWeight > Mage::getStoreConfig('labor/laborrate/threshold')) {
			$laborRate = Mage::getStoreConfig('labor/laborrate/morethanthreshold');
			if(($totalWeight > 0) && ($laborRate > 0)) $product->setLabor($totalWeight*$laborRate);
		} else {
			$laborRate = Mage::getStoreConfig('labor/laborrate/lessthanthreshold');
			if(($totalWeight > 0) && ($laborRate > 0)) $product->setLabor($laborRate);
		}
		
		$listingPrice = $listingPrice + $product->getLabor();
		
		$taxRate = Mage::getStoreConfig('dnktax/taxrate/rate');
		if($taxRate > 0) $product->setTaxrate($listingPrice*$taxRate/100);
		
		if($listingPrice > 0) $product->setListingprice($listingPrice+$product->getTaxrate());
		
		if($price > 0) $product->setPrice($price+$product->getTaxrate()+$product->getLabor());
		
	}
 
    public function catalogModelProductDuplicate($observer)
    {
        $event = $observer->getEvent();
 
        $currentProduct = $event->getCurrentProduct();
        $newProduct = $event->getNewProduct();
 
        $newProduct->setRates($currentProduct->getRates());
        $newProduct->setStoneRates($currentProduct->getStoneRates());
    }
 	
	public function modifyPrice(Varien_Event_Observer $obs)
    {
        // Get the quote item
        $item = $obs->getQuoteItem();
        // Ensure we have the parent item, if it has one
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        // Load the custom price
		
        $path = Mage::getModel('core/cookie')->getPath();
        $domain = Mage::getModel('core/cookie')->getDomain();
        $price = Mage::getModel('core/cookie')->get('sum');
        
		//$price = "your custom price logic";
        // Set the custom price
        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        // Enable super mode on the product.
        $item->getProduct()->setIsSuperMode(true);
    }
}