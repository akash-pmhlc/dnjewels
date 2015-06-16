<?php
class Dnk_Certificates_Block_Adminhtml_Certificates_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("certificates_form", array("legend"=>Mage::helper("certificates")->__("Certificate information")));

				
						$fieldset->addField("certificate", "text", array(
						"label" => Mage::helper("certificates")->__("Certificate"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "certificate",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getCertificatesData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCertificatesData());
					Mage::getSingleton("adminhtml/session")->setCertificatesData(null);
				} 
				elseif(Mage::registry("certificates_data")) {
				    $form->setValues(Mage::registry("certificates_data")->getData());
				}
				return parent::_prepareForm();
		}
}
