<?php
class Dnk_Sizes_Block_Adminhtml_Sizes_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("sizes_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("sizes")->__("Size Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("sizes")->__("Size Information"),
				"title" => Mage::helper("sizes")->__("Size Information"),
				"content" => $this->getLayout()->createBlock("sizes/adminhtml_sizes_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
