<?php
class Dnk_Shapes_Block_Adminhtml_Shapes_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("shapes_form", array("legend"=>Mage::helper("shapes")->__("Shape information")));

				
						$fieldset->addField("shape", "text", array(
						"label" => Mage::helper("shapes")->__("Shape"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "shape",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getShapesData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getShapesData());
					Mage::getSingleton("adminhtml/session")->setShapesData(null);
				} 
				elseif(Mage::registry("shapes_data")) {
				    $form->setValues(Mage::registry("shapes_data")->getData());
				}
				return parent::_prepareForm();
		}
}
