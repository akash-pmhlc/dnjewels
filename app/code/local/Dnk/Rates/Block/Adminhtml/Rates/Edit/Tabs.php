<?php
class Dnk_Rates_Block_Adminhtml_Rates_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("rates_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("rates")->__("Rate Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("rates")->__("Rate Information"),
				"title" => Mage::helper("rates")->__("Rate Information"),
				"content" => $this->getLayout()->createBlock("rates/adminhtml_rates_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
