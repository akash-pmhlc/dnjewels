<?php
class Dnk_Stone_Block_Adminhtml_Stones_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("stone_form", array("legend"=>Mage::helper("stone")->__("Stone information")));

				
						$fieldset->addField("name", "text", array(
						"label" => Mage::helper("stone")->__("Stone Name"),
						"name" => "name",
						));
					
						$fieldset->addField("rate", "text", array(
						"label" => Mage::helper("stone")->__("Rate"),
						"name" => "rate",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getStonesData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getStonesData());
					Mage::getSingleton("adminhtml/session")->setStonesData(null);
				} 
				elseif(Mage::registry("stones_data")) {
				    $form->setValues(Mage::registry("stones_data")->getData());
				}
				return parent::_prepareForm();
		}
}
