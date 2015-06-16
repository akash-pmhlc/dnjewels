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
		
		if (is_null($postProductOptions)) {
			$productId = $product->getEntityId();
			$postProductOptions = $this->getProductOptionArray($productId);
		}

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

			$diamond['price'] = $product->getDiamondcharge();
			$diamond['certificate-selected'] = $certificateSelected;
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
			$stone['price'] = $product->getStonecharge();
		}
		
		$goldWeight = $product->getGoldweight();
		$totalWeight = $goldWeight + ($totalWeight*0.2);
		
		$goldRate = Mage::getStoreConfig('gold/goldrate/rate24k');
		
		if($goldWeight > 0) {
			$goldRate14K = Mage::getStoreConfig('gold/goldrate/rate14k');
			$goldRate14kString = $goldWeight*$goldRate*$goldRate14K/10;
			$goldRate18K = Mage::getStoreConfig('gold/goldrate/rate18k');
			$goldRate18kString = $goldWeight*$goldRate*$goldRate18K/10;
		
			$product->setGoldrate14k($goldRate14kString);
			
			$product->setGoldrate18k($goldRate18kString);
			
			if($product->getGoldrate14k() != '') {
				$goldSelected = '14K';
				$listingPrice = $price + $product->getGoldrate14k();
				$gold['price'] = $product->getGoldrate14k();
			} elseif ($product->getGoldrate18k() != '') {
				$goldSelected = '18K';
				$listingPrice = $price + $product->getGoldrate18k();
				$gold['price'] = $product->getGoldrate18k();
			}

		}

		if($totalWeight > 0) $product->setTotalweight($totalWeight);
		
		$productOptions = $postProductOptions;
		foreach ($postProductOptions as $parentKey => $postProductOption) {

			if($postProductOption['title'] == 'Gold Purity') {

				foreach ($postProductOption['values'] as $childKey => $postProductOptionValue) {
					if($postProductOptionValue['title'] == '14K') {
						$productOptions[$parentKey]['values'][$childKey]['price'] = $product->getGoldrate14k();
					}
					if($postProductOptionValue['title'] == '18K') {
						$productOptions[$parentKey]['values'][$childKey]['price'] = $product->getGoldrate18k();
					}
					
					if($postProductOptionValue['title'] == $goldSelected) {
						if (($postProductOption['option_id'] > 0) && ($postProductOptionValue['option_type_id'] > 0)) {
							$option_id = $postProductOption['option_id'];
							$option_type_id = $postProductOptionValue['option_type_id'];
						
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
						} else {
							Mage::getSingleton('adminhtml/session')->setSaveCommitAfter('1');
							Mage::getSingleton('adminhtml/session')->setSaveCommitAfterGoldPurity($goldSelected);
						}
					}
					
				}

			}  elseif($postProductOption['title'] == 'Diamond Color & Purity') {

				foreach ($postProductOption['values'] as $childKey => $postProductOptionValue) {
					
					$productOptions[$parentKey]['values'][$childKey]['price'] = $pricePerCertificate[$postProductOptionValue['title']];

					if($postProductOptionValue['title'] == $certificateSelected) {
						if (($postProductOption['option_id'] > 0) && ($postProductOptionValue['option_type_id'] > 0)) {
							$option_id = $postProductOption['option_id'];
							$option_type_id = $postProductOptionValue['option_type_id'];
						
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
						} else {
							Mage::getSingleton('adminhtml/session')->setSaveCommitAfter('1');
							Mage::getSingleton('adminhtml/session')->setSaveCommitAfterDiamondPurity($certificateSelected);
						}
					}
					
				}
				
			}  elseif ($postProductOption['title'] == 'Gold Color') {
				$color = 'Yellow';
				if ($postProductOption['option_id'] > 0) {
					foreach ($postProductOption['values'] as $childKey => $postProductOptionValue) {
						
						if($postProductOptionValue['title'] == $color) {
							$gold_color_option_type_id = $postProductOptionValue['option_type_id'];
						} else {
							$option_type_id_if_color_not_found = $postProductOptionValue['option_type_id'];
						}
					}

					$option_id = $postProductOption['option_id'];

					if(!isset($gold_color_option_type_id)) {
						$option_type_id = $option_type_id_if_color_not_found;
					} else {
						$option_type_id = $gold_color_option_type_id;
					}

					if ($option_type_id > 0) {
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
					} else {
							Mage::getSingleton('adminhtml/session')->setSaveCommitAfter('1');
					}

				}
			}

		}

		$product->setData('can_save_custom_options',1);
		$product->setProductOptions($productOptions);
		
		$listingPrice = $listingPrice + $product->getCertificatecharge();
		
		$listingPrice = $listingPrice + $minDiamondCertificatePrice;
		
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
		
		if($listingPrice > 0) $product->setListingprice(round($listingPrice+$product->getTaxrate()));
		
		if($price > 0) $product->setPrice(round($price+$product->getTaxrate()+$product->getLabor()));
		
	}

 
    public function catalogProductAfterSave($observer)
    {
        $saveDefaultOption = Mage::getSingleton('adminhtml/session')->getSaveCommitAfter();
        if ($saveDefaultOption == 1) {

        	$productOptions = $observer->getEvent()->getProduct()->getProductOptions();

			$matchArray[0] = Mage::getSingleton('adminhtml/session')->getSaveCommitAfterDiamondPurity();
			$matchArray[1] = Mage::getSingleton('adminhtml/session')->getSaveCommitAfterGoldPurity();

    		Mage::getSingleton('adminhtml/session')->unsSaveCommitAfter();
        	Mage::getSingleton('adminhtml/session')->unsSaveCommitAfterDiamondPurity();
        	Mage::getSingleton('adminhtml/session')->unsSaveCommitAfterGoldPurity();

			foreach ($productOptions as $parentKey => $productOptionValues) {
				foreach ($productOptionValues as $childKey => $productOptionValueArray) {
					if (in_array($productOptionValueArray['title'], $matchArray)) {
						
						$option_id = $parentKey;
						$option_type_id = $childKey;

						$model = Mage::getModel('customoption/customoption');
						$tablename=Mage::getModel('customoption/customoption')->getResource()->getTableName();
						$connection=Mage::getSingleton('core/resource')->getConnection('core_write');
						$option = Mage::helper('customoption')->getOption($option_id);
						if(!$option) {
							$model->setData('option_id',$option_id);
							$model->setData('option_type_id',$option_type_id);
							$model->save();
							$model->unsetData();
						}
					}

					if ($productOptionValues['title'] == 'Gold Color') {
						$goldColorArray[$productOptionValueArray['title']][0] = $parentKey;
						$goldColorArray[$productOptionValueArray['title']][1] = $childKey;
					}
				}
			}

			if (isset($goldColorArray['Yellow'])) {
				$color_option_id = $goldColorArray['Yellow'][0];
				$color_option_type_id = $goldColorArray['Yellow'][1];
			} elseif (isset($goldColorArray['White'])) {
				$color_option_id = $goldColorArray['White'][0];
				$color_option_type_id = $goldColorArray['White'][1];
			} elseif (isset($goldColorArray['Pink'])) {
				$color_option_id = $goldColorArray['Pink'][0];
				$color_option_type_id = $goldColorArray['Pink'][1];
			}

			if (isset($color_option_id) && isset($color_option_type_id)) {
				$option_id = $color_option_id;
				$option_type_id = $color_option_type_id;

				$model = Mage::getModel('customoption/customoption');
				$tablename=Mage::getModel('customoption/customoption')->getResource()->getTableName();
				$connection=Mage::getSingleton('core/resource')->getConnection('core_write');
				$option = Mage::helper('customoption')->getOption($option_id);
				if(!$option) {
					$model->setData('option_id',$option_id);
					$model->setData('option_type_id',$option_type_id);
					$model->save();
					$model->unsetData();
				}
			}

        }
	}

	public function getProductOptionArray($productId)
	{
		$product = Mage::getModel('catalog/product')->load($productId);
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

		return $productOptions;
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