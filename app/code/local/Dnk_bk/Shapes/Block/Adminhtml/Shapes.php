<?php


class Dnk_Shapes_Block_Adminhtml_Shapes extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_shapes";
	$this->_blockGroup = "shapes";
	$this->_headerText = Mage::helper("shapes")->__("Shapes Manager");
	$this->_addButtonLabel = Mage::helper("shapes")->__("Add New Shape");
	parent::__construct();
	
	}

}