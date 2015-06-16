<?php


class Dnk_Stone_Block_Adminhtml_Stones extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_stones";
	$this->_blockGroup = "stone";
	$this->_headerText = Mage::helper("stone")->__("Stones Manager");
	$this->_addButtonLabel = Mage::helper("stone")->__("Add New Stone");
	parent::__construct();
	
	}

}