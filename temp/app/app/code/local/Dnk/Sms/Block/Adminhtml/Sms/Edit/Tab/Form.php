<?php

class Dnk_Sms_Block_Adminhtml_Sms_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('sms_form', array('legend'=>Mage::helper('sms')->__('Item information')));
     
      $fieldset->addField('sms_template_name', 'text', array(
          'label'     => Mage::helper('sms')->__('SMS Template Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'sms_template_name',
      ));
      
      $fieldset->addField('sms_template_content', 'editor', array(
          'name'      => 'sms_template_content',
          'label'     => Mage::helper('sms')->__('SMS Template Content'),
          'title'     => Mage::helper('sms')->__('SMS Template Content'),
          'style'     => 'width:700px; height:100px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
	
      // Visible only to user having rights for approval.
      if(Mage::getSingleton('admin/session')->isAllowed('homepage/sms/approved'))
      {
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('sms')->__('Approved Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 2,
                    'label'     => Mage::helper('sms')->__('Not approved'),
                ),
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('sms')->__('Approved'),
                ),             
            ),
        ));

        $fieldset->addField('approved_by', 'text', array(
            'label'     => Mage::helper('sms')->__('Approved By Service Provider'),
            'name'      => 'approved_by',
            'value'  => 'shailendra',
        ));

        $fieldset->addField('comment', 'editor', array(
            'name'      => 'comment',
            'label'     => Mage::helper('sms')->__('Comment'),
            'title'     => Mage::helper('sms')->__('Comment'),
            'style'     => 'width:700px; height:125px;',
            'wysiwyg'   => false,
        ));
      }
      
      if ( Mage::getSingleton('adminhtml/session')->getSmsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSmsData());
          Mage::getSingleton('adminhtml/session')->setSmsData(null);
      } elseif ( Mage::registry('sms_data') ) {
          $form->setValues(Mage::registry('sms_data')->getData());
      }
      return parent::_prepareForm();
  }
}