<?php
class Dnk_Sms_Block_Adminhtml_Sms extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_sms';
    $this->_blockGroup = 'sms';
    $this->_headerText = Mage::helper('sms')->__('SMS Template Manager');
    $this->_addButtonLabel = Mage::helper('sms')->__('Add SMS Template');
    parent::__construct();
  }
}