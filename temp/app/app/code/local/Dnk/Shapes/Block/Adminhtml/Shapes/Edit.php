<?php
	
class Dnk_Shapes_Block_Adminhtml_Shapes_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "shape_id";
				$this->_blockGroup = "shapes";
				$this->_controller = "adminhtml_shapes";
				$this->_updateButton("save", "label", Mage::helper("shapes")->__("Save Shape"));
				$this->_updateButton("delete", "label", Mage::helper("shapes")->__("Delete Shape"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("shapes")->__("Save And Continue Edit"),
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
				if( Mage::registry("shapes_data") && Mage::registry("shapes_data")->getId() ){

				    return Mage::helper("shapes")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("shapes_data")->getId()));

				} 
				else{

				     return Mage::helper("shapes")->__("Add Shape");

				}
		}
}