<?php


class Dnk_Sizes_Block_Adminhtml_Sizes extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_sizes";
	$this->_blockGroup = "sizes";
	$this->_headerText = Mage::helper("sizes")->__("Sizes Manager");
	$this->_addButtonLabel = Mage::helper("sizes")->__("Add New Size");
	parent::__construct();
	
	}

}