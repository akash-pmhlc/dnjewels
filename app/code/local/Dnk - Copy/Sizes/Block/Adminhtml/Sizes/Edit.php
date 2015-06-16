<?php
	
class Dnk_Sizes_Block_Adminhtml_Sizes_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "size_id";
				$this->_blockGroup = "sizes";
				$this->_controller = "adminhtml_sizes";
				$this->_updateButton("save", "label", Mage::helper("sizes")->__("Save Size"));
				$this->_updateButton("delete", "label", Mage::helper("sizes")->__("Delete Size"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("sizes")->__("Save And Continue Edit"),
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
				if( Mage::registry("sizes_data") && Mage::registry("sizes_data")->getId() ){

				    return Mage::helper("sizes")->__("Edit Size '%s'", $this->htmlEscape(Mage::registry("sizes_data")->getId()));

				} 
				else{

				     return Mage::helper("sizes")->__("Add Size");

				}
		}
}