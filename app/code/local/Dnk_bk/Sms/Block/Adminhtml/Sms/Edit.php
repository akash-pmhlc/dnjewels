<?php

class Dnk_Sms_Block_Adminhtml_Sms_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'sms';
        $this->_controller = 'adminhtml_sms';
        
        $this->_updateButton('save', 'label', Mage::helper('sms')->__('Save SMS Template'));
        $this->_updateButton('delete', 'label', Mage::helper('sms')->__('Delete SMS Template'));
        
        if((Mage::getSingleton('admin/session')->isAllowed('homepage/sms/approved') != 1) && (Mage::getSingleton('admin/session')->isAllowed('homepage/sms/create') == 1))
        {
           $this->_removeButton('delete');
        }
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('sms_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'sms_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'sms_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('sms_data') && Mage::registry('sms_data')->getId() ) {
            return Mage::helper('sms')->__("Edit SMS Template '%s'", $this->htmlEscape(Mage::registry('sms_data')->getSmsTemplateName()));
        } else {
            return Mage::helper('sms')->__('Add SMS Template');
        }
    }
}