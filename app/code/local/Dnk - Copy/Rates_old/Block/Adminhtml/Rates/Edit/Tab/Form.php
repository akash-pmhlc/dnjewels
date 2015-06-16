<?php
class Dnk_Rates_Block_Adminhtml_Rates_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {

    $form = new Varien_Data_Form();
    $this->setForm($form);
    $fieldset = $form->addFieldset("rates_form", array("legend"=>Mage::helper("rates")->__("Rate information")));

        
      $fieldset->addField('shape', 'select', array(
      'label'     => Mage::helper('rates')->__('Shape'),
      'values'   => Dnk_Rates_Model_Shapes_Options::getOptionArray(),
      'name' => 'shape',     
      "class" => "required-entry",
      "required" => true,
      ));    
      $fieldset->addField('size', 'select', array(
      'label'     => Mage::helper('rates')->__('Size'),
      'values'   => Dnk_Rates_Model_Sizes_Options::getOptionArray(),
      'name' => 'size',
      "class" => "required-entry",
      "required" => true,
      ));    
      $fieldset->addField('certificate', 'select', array(
      'label'     => Mage::helper('rates')->__('Certificate'),
      'values'   => Dnk_Rates_Model_Certificates_Options::getOptionArray(),
      'name' => 'certificate',
      "class" => "required-entry",
      "required" => true,
      ));
      $fieldset->addField("rate", "text", array(
      "label" => Mage::helper("rates")->__("Rate"),
      "name" => "rate",
      "class" => "required-entry",
      "required" => true,
      ));
     

    if (Mage::getSingleton("adminhtml/session")->getRatesData())
    {
     $form->setValues(Mage::getSingleton("adminhtml/session")->getRatesData());
     Mage::getSingleton("adminhtml/session")->setRatesData(null);
    } 
    elseif(Mage::registry("rates_data")) {
        $form->setValues(Mage::registry("rates_data")->getData());
    }
    return parent::_prepareForm();
  }
}
