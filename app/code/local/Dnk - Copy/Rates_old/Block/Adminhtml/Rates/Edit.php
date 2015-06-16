<?php
	
class Dnk_Rates_Block_Adminhtml_Rates_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "rate_id";
				$this->_blockGroup = "rates";
				$this->_controller = "adminhtml_rates";
				$this->_updateButton("save", "label", Mage::helper("rates")->__("Save Rate"));
				$this->_updateButton("delete", "label", Mage::helper("rates")->__("Delete Rate"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("rates")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);
				
				$rateId = $this->getRequest()->getParam('id');

				if (! empty($rateId)) {
					$this->_addButton("updateproductrates", array(
						"label"     => Mage::helper("rates")->__("Update Product Prices"),
						'onclick'   => 'setLocation(\'' . $this->getUpdateProductPriceUrl() . '\')',
					), -200);
				}

				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("rates_data") && Mage::registry("rates_data")->getId() ){

				    return Mage::helper("rates")->__("Edit Rate '%s'", $this->htmlEscape(Mage::registry("rates_data")->getId()));

				} 
				else{

				     return Mage::helper("rates")->__("Add Rate");

				}
		}
				
		public function getUpdateProductPriceUrl()
		{
			return $this->getUrl('*/adminhtml_rates/updateproductrates', array('id' => $this->getRequest()->getParam('id')));
		}
}