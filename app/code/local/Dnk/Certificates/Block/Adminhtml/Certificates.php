<?php


class Dnk_Certificates_Block_Adminhtml_Certificates extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_certificates";
	$this->_blockGroup = "certificates";
	$this->_headerText = Mage::helper("certificates")->__("Certificates Manager");
	$this->_addButtonLabel = Mage::helper("certificates")->__("Add New Certificate");
	parent::__construct();
	
	}

}