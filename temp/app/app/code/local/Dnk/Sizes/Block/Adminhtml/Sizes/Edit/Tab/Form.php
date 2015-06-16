<?php
class Dnk_Sizes_Block_Adminhtml_Sizes_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("sizes_form", array("legend"=>Mage::helper("sizes")->__("Size information")));

				
						$fieldset->addField("size", "text", array(
						"label" => Mage::helper("sizes")->__("Size"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "size",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getSizesData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getSizesData());
					Mage::getSingleton("adminhtml/session")->setSizesData(null);
				} 
				elseif(Mage::registry("sizes_data")) {
				    $form->setValues(Mage::registry("sizes_data")->getData());
				}
				return parent::_prepareForm();
		}
}
