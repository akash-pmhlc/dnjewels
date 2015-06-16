<?php


class Dnk_Rates_Block_Adminhtml_Rates extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_rates";
	$this->_blockGroup = "rates";
	$this->_headerText = Mage::helper("rates")->__("Rates Manager");
	$this->_addButtonLabel = Mage::helper("rates")->__("Add New Rate");
	parent::__construct();
	
	}

}