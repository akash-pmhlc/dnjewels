<?php

class Dnk_Rates_Block_Adminhtml_Rates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("ratesGrid");
				$this->setDefaultSort("rate_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("rates/rates")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("rate_id", array(
				"header" => Mage::helper("rates")->__("Rate ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "rate_id",
				));
                
						$this->addColumn('shape', array(
						'header' => Mage::helper('rates')->__('Shape'),
						'index' => 'shape',
						'type' => 'options',
						'options'=>Dnk_Rates_Model_Shapes_Options::getOptionArray(),				
						));
						
						$this->addColumn('size', array(
						'header' => Mage::helper('rates')->__('Size'),
						'index' => 'size',
						'type' => 'options',		
						'options'=>Dnk_Rates_Model_Sizes_Options::getOptionArray(),										
						));
						
						$this->addColumn('certificate', array(
						'header' => Mage::helper('rates')->__('Certificate'),
						'index' => 'certificate',
						'type' => 'options',	
						'options'=>Dnk_Rates_Model_Certificates_Options::getOptionArray(),										
						));
						
				$this->addColumn("rate", array(
				"header" => Mage::helper("rates")->__("Rate"),
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
			$this->setMassactionIdField('rate_id');
			$this->getMassactionBlock()->setFormFieldName('rate_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_rates', array(
					 'label'=> Mage::helper('rates')->__('Remove Rates'),
					 'url'  => $this->getUrl('*/adminhtml_rates/massRemove'),
					 'confirm' => Mage::helper('rates')->__('Are you sure?')
				));
			return $this;
		}
					

}