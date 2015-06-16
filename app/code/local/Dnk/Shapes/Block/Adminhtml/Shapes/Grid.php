<?php

class Dnk_Shapes_Block_Adminhtml_Shapes_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("shapesGrid");
				$this->setDefaultSort("shape_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("shapes/shapes")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("shape_id", array(
				"header" => Mage::helper("shapes")->__("Shape ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "shape_id",
				));
                
				$this->addColumn("shape", array(
				"header" => Mage::helper("shapes")->__("Shape"),
				"index" => "shape",
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
			$this->setMassactionIdField('shape_id');
			$this->getMassactionBlock()->setFormFieldName('shape_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_shapes', array(
					 'label'=> Mage::helper('shapes')->__('Remove Shapes'),
					 'url'  => $this->getUrl('*/adminhtml_shapes/massRemove'),
					 'confirm' => Mage::helper('shapes')->__('Are you sure?')
				));
			return $this;
		}
			

}