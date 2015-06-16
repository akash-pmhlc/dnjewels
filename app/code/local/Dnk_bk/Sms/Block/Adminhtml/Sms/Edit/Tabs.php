<?php

class Dnk_Sms_Block_Adminhtml_Sms_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('sms_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('sms')->__('SMS Template Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('sms')->__('SMS Template Information'),
          'title'     => Mage::helper('sms')->__('SMS Template Information'),
          'content'   => $this->getLayout()->createBlock('sms/adminhtml_sms_edit_tab_form')->toHtml(),
      ));
      
      $this->addTab('comment_section', array(
            'label'     => Mage::helper('sms')->__('SMS Approval History'),
            'title'     => Mage::helper('sms')->__('SMS Approval History'),
            'content'   => $this->getLayout()->createBlock('sms/adminhtml_sms_edit_tab_comment')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}