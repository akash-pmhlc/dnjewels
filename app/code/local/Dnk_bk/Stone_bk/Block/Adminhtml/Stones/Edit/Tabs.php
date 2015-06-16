<?php
class Dnk_Stone_Block_Adminhtml_Stones_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("stones_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("stone")->__("Stone Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("stone")->__("Stone Information"),
				"title" => Mage::helper("stone")->__("Stone Information"),
				"content" => $this->getLayout()->createBlock("stone/adminhtml_stones_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
