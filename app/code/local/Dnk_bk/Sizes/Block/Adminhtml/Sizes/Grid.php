<?php

class Dnk_Sizes_Block_Adminhtml_Sizes_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("sizesGrid");
				$this->setDefaultSort("size_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("sizes/sizes")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("size_id", array(
				"header" => Mage::helper("sizes")->__("Size ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "size_id",
				));
                
				$this->addColumn("size", array(
				"header" => Mage::helper("sizes")->__("Size"),
				"index" => "size",
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
			$this->setMassactionIdField('size_id');
			$this->getMassactionBlock()->setFormFieldName('size_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_sizes', array(
					 'label'=> Mage::helper('sizes')->__('Remove Sizes'),
					 'url'  => $this->getUrl('*/adminhtml_sizes/massRemove'),
					 'confirm' => Mage::helper('sizes')->__('Are you sure?')
				));
			return $this;
		}
			

}