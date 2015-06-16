<?php
class Dnk_Shapes_Block_Adminhtml_Shapes_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("shapes_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("shapes")->__("Shape Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("shapes")->__("Shape Information"),
				"title" => Mage::helper("shapes")->__("Shape Information"),
				"content" => $this->getLayout()->createBlock("shapes/adminhtml_shapes_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
