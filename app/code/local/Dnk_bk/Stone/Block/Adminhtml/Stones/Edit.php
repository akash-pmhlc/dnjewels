<?php
	
class Dnk_Stone_Block_Adminhtml_Stones_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "stone_id";
				$this->_blockGroup = "stone";
				$this->_controller = "adminhtml_stones";
				$this->_updateButton("save", "label", Mage::helper("stone")->__("Save Stone"));
				$this->_updateButton("delete", "label", Mage::helper("stone")->__("Delete Stone"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("stone")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);
				
				$stoneId = $this->getRequest()->getParam('id');

				/*if (! empty($stoneId)) {
					$this->_addButton("updateproductrates", array(
						"label"     => Mage::helper("stone")->__("Update Product Prices"),
						'onclick'   => 'setLocation(\'' . $this->getUpdateProductPriceUrl() . '\')',
					), -200);
				}*/



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("stones_data") && Mage::registry("stones_data")->getId() ){

				    return Mage::helper("stone")->__("Edit Stone '%s'", $this->htmlEscape(Mage::registry("stones_data")->getId()));

				} 
				else{

				     return Mage::helper("stone")->__("Add Stone");

				}
		}
				
		public function getUpdateProductPriceUrl()
		{
			return $this->getUrl('*/adminhtml_stones/updateproductrates', array('id' => $this->getRequest()->getParam('id')));
		}
}