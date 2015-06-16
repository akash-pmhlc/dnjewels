<?php

class Dnk_Stone_Block_Adminhtml_Stones_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("stonesGrid");
				$this->setDefaultSort("stone_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("stone/stones")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("stone_id", array(
				"header" => Mage::helper("stone")->__("Stone ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "stone_id",
				));
                
				$this->addColumn("name", array(
				"header" => Mage::helper("stone")->__("Stone Name"),
				"index" => "name",
				));
				$this->addColumn("rate", array(
				"header" => Mage::helper("stone")->__("Rate"),
				"index" => "rate",
				));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('stone_id');
			$this->getMassactionBlock()->setFormFieldName('stone_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_stones', array(
					 'label'=> Mage::helper('stone')->__('Remove Stones'),
					 'url'  => $this->getUrl('*/adminhtml_stones/massRemove'),
					 'confirm' => Mage::helper('stone')->__('Are you sure?')
				));
			return $this;
		}
			

}