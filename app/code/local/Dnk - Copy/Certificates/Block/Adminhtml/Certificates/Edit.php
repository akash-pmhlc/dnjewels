<?php
	
class Dnk_Certificates_Block_Adminhtml_Certificates_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "certificate_id";
				$this->_blockGroup = "certificates";
				$this->_controller = "adminhtml_certificates";
				$this->_updateButton("save", "label", Mage::helper("certificates")->__("Save Certificate"));
				$this->_updateButton("delete", "label", Mage::helper("certificates")->__("Delete Certificate"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("certificates")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("certificates_data") && Mage::registry("certificates_data")->getId() ){

				    return Mage::helper("certificates")->__("Edit Certificate '%s'", $this->htmlEscape(Mage::registry("certificates_data")->getId()));

				} 
				else{

				     return Mage::helper("certificates")->__("Add Certificate");

				}
		}
}