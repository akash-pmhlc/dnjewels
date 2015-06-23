<?php
class Magebuzz_Customoption_Block_Catalog_Product_View_Options_Type_Select
    extends Mage_Catalog_Block_Product_View_Options_Type_Select
{

    public function getValuesHtml()
    {
        $_option = $this->getOption();
		$option_data=$_option->getData();
		$default_value=Mage::getModel('customoption/customoption')->getResource()->getValue($option_data['option_id']);
        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $_option->getId());
        $store = $this->getProduct()->getStore();
        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN
            || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {

            $require = ($_option->getIsRequire()) ? ' required-entry' : '';
            $extraParams = '';
            $select = $this->getLayout()->createBlock('core/html_select')
                ->setData(array(
                    'id' => 'select_'.$_option->getId(),
                    'class' => $require.' product-custom-option'
                ));
			
            $elementId = $_option->getId();
            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN) {
                $select->setName('options['.$_option->getid().']')
                    ->addOption('', $this->__('-- Please Select --'));
            } else {
                $select->setName('options['.$_option->getid().'][]');
                $select->setClass('multiselect'.$require.' product-custom-option');
            }
            foreach ($_option->getValues() as $_value) {
                $goldRate9K = $this->getProduct()->getGoldrate9k();
                $goldRate14K = $this->getProduct()->getGoldrate14k();
                $goldRate18K = $this->getProduct()->getGoldrate18k();
                $goldRate9KArray = explode(',', $goldRate9K);
                $goldRate14KArray = explode(',', $goldRate14K);
                $goldRate18KArray = explode(',', $goldRate18K);
                foreach ($goldRate9KArray as $optionRate) {
                    $optionRateArray = explode('-', $optionRate);
                    if ($optionRateArray[1] == $_value->getTitle()) {
                        $option9KPrice = $optionRateArray[0];
                        break;
                    }
                }
                foreach ($goldRate14KArray as $optionRate) {
                    $optionRateArray = explode('-', $optionRate);
                    if ($optionRateArray[1] == $_value->getTitle()) {
                        $option14KPrice = $optionRateArray[0];
                        break;
                    }
                }
                foreach ($goldRate18KArray as $optionRate) {
                    $optionRateArray = explode('-', $optionRate);
                    if ($optionRateArray[1] == $_value->getTitle()) {
                        $option18KPrice = $optionRateArray[0];
                        break;
                    }
                }
                /*$priceStr = $this->_formatPrice(array(
                    'is_percent' => ($_value->getPriceType() == 'percent') ? true : false,
                    'pricing_value' => $_value->getPrice(true)
                ), false);*/
				
				if ($_value->getOptionTypeId() == $default_value[0]['option_type_id']) {
					$select->addOption(
	                    $_value->getOptionTypeId(),
	                    //$_value->getTitle() . ' ' . $priceStr . '',
						$_value->getTitle(),
	                    array('price' => $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false),'9K' =>$option9KPrice,'14K' =>$option14KPrice,'18K' =>$option18KPrice,
							'selected' => 'selected'
						)
	                );

				}
				else {
	                $select->addOption(
	                    $_value->getOptionTypeId(),
	                    //$_value->getTitle() . ' ' . $priceStr . '',
						$_value->getTitle(),
	                    array('price' => $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false),'14K' =>$option14KPrice,'18K' =>$option18KPrice)
	                );
				}
            }
            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                $extraParams = ' multiple="multiple"';
            }
            if (!$this->getSkipJsReloadPrice()) {
                $extraParams .= ' onchange="opConfig.reloadPrice();changeInSelect('.$elementId.');"';
            }
            $select->setExtraParams($extraParams);

            if ($configValue) {
                $select->setValue($configValue);
            }

            return $select->getHtml();
        }

        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO
            || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
            ) {
            //$selectHtml = '<ul id="options-'.$_option->getId().'-list" class="options-list">';
			$selectHtml = '<ul id="options-'.$_option->getId().'-list" class="options-list" style="display:inline-flex;">';
            $require = ($_option->getIsRequire()) ? ' validate-one-required-by-name' : '';
            $arraySign = '';
            switch ($_option->getType()) {
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO:
                    $type = 'radio';
                    $class = 'radio';
                    if (!$_option->getIsRequire()) {
                        $selectHtml .= '<li><input type="radio" id="options_'.$_option->getId().'" class="'.$class.' product-custom-option" name="options['.$_option->getId().']"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"') . ' value="" checked="checked" /><span class="label"><label for="options_'.$_option->getId().'" id="label_options_'.$_option->getId().'">' . $this->__('None') . '</label></span></li>';
                    }
                    break;
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX:
                    $type = 'checkbox';
                    $class = 'checkbox';
                    $arraySign = '[]';
                    break;
            }
            $count = 1;
            foreach ($_option->getValues() as $_value) {
                $count++;

                /*$priceStr = $this->_formatPrice(array(
                    'is_percent' => ($_value->getPriceType() == 'percent') ? true : false,
                    'pricing_value' => $_value->getPrice(true)
                ));*/

                $htmlValue = $_value->getOptionTypeId();
                if ($arraySign) {
                    $checked = (is_array($configValue) && in_array($htmlValue, $configValue)) ? 'checked' : '';
                } else {
                    $checked = $configValue == $htmlValue ? 'checked' : '';
                }
				$checked=NULL;
				if ($_value->getOptionTypeId() == $default_value[0]['option_type_id']) {
					$checked='checked="checked"';
				}
                $selectHtml .= '<li>' .
                               '<input optioncategory="'.$_option->getTitle().'" type="'.$type.'" class="'.$class.' '.$require.' product-custom-option"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"') . ' name="options['.$_option->getId().']'.$arraySign.'" id="options_'.$_option->getId().'_'.$count.'" value="' . $htmlValue . '" ' . $checked . ' price="' . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />' .
                               '<span class="label"><label style="width:auto;"for="options_'.$_option->getId().'_'.$count.'" id="label_options_'.$_option->getId().'_'.$count.'">'.$_value->getTitle().'</label></span>';
                if ($_option->getIsRequire()) {
                    $selectHtml .= '<script type="text/javascript">' .
                                    '$(\'options_'.$_option->getId().'_'.$count.'\').advaiceContainer = \'options-'.$_option->getId().'-container\';' .
                                    '$(\'options_'.$_option->getId().'_'.$count.'\').callbackFunction = \'validateOptionsCallback\';' .
                                   '</script>';
                }
                $selectHtml .= '</li>';
            }
            $selectHtml .= '</ul>';

            return $selectHtml;
        }
    }

}
