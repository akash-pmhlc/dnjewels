<?php

class Dnk_Certificates_Block_Adminhtml_Certificates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("certificatesGrid");
				$this->setDefaultSort("certificate_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("certificates/certificates")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("certificate_id", array(
				"header" => Mage::helper("certificates")->__("Certificate ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "certificate_id",
				));
                
				$this->addColumn("certificate", array(
				"header" => Mage::helper("certificates")->__("Certificate"),
				"index" => "certificate",
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
			$this->setMassactionIdField('certificate_id');
			$this->getMassactionBlock()->setFormFieldName('certificate_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_certificates', array(
					 'label'=> Mage::helper('certificates')->__('Remove Certificates'),
					 'url'  => $this->getUrl('*/adminhtml_certificates/massRemove'),
					 'confirm' => Mage::helper('certificates')->__('Are you sure?')
				));
			return $this;
		}
			

}