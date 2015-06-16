<?php
class Dnk_Certificates_Block_Adminhtml_Certificates_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("certificates_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("certificates")->__("Certificate Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("certificates")->__("Certificate Information"),
				"title" => Mage::helper("certificates")->__("Certificate Information"),
				"content" => $this->getLayout()->createBlock("certificates/adminhtml_certificates_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
