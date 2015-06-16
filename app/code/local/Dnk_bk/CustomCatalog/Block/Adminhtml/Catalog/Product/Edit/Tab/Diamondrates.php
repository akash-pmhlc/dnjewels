<?php

class Dnk_CustomCatalog_Block_Adminhtml_Catalog_Product_Edit_Tab_Diamondrates extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	* Set grid params
	*
	*/
	public function __construct() {

		parent::__construct();
		$this->setId('diamondrates_grid');
		$this->setDefaultSort('rate_id_child');
		$this->setUseAjax(true);
		if ($this->_getProduct()->getId()) {
			$this->setDefaultFilter(array('rate_ids_in' => 1));
		}
		
		if ($this->isReadonly()) {
			$this->setFilterVisibility(false);
		}
	}

	/**
	* Retirve currently edited product model
	*
	* @return Mage_Catalog_Model_Product
	*/
	
	protected function _getProduct() {
		return Mage::registry('current_product');
	}
	
	/**
	* Add filter
	*
	* @param object $column
	* @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Custom
	*/

	protected function _addColumnFilterToCollection($column) {

	// Set custom filter for in rates flag
		if ($column->getId() == 'rateids_in_product') {
			$rateIds = $this->_getSelectedRates();
			if (empty($rateIds)) {
				$rateIds = 0;
			}
		
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('rate_id_child', array('in' => $rateIds));
			} else {
				if($rateIds) {
					$this->getCollection()->addFieldToFilter('rate_id_child', array('nin' => $rateIds));
				}
			}
		
		} else {
			parent::_addColumnFilterToCollection($column);
		}
	
		return $this;
	}

	/**
	* Prepare collection
	*
	* @return Mage_Adminhtml_Block_Widget_Grid
	*/
	protected function _prepareCollection() {
		$collection = Mage::getModel('rates/childrates')->getCollection();
			//->getProductCollection()
			//->setProduct($this->_getProduct())
			//->addAttributeToSelect('*');

		if ($this->isReadonly()) {
			$rateIds = $this->_getSelectedRates();
			if (empty($rateIds)) {
				$rateIds = array(0);
			}
			$collection->addFieldToFilter('rate_id_child', array('in' => $rateIds));
		}

		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	* Checks when this block is readonly
	*
	* @return boolean
	*/
	public function isReadonly() {
		return $this->_getProduct()->getCustomReadonly();
	}
	
	/**
	* Add columns to grid
	*
	* @return Mage_Adminhtml_Block_Widget_Grid
	*/

	protected function _prepareColumns() {
		if (!$this->isReadonly()) {   
			$this->addColumn('rateids_in_product', array(
					'header_css_class' => 'a-center',
					'type' => 'checkbox',
					'name' => 'rateids_in_product',
					'values' => $this->_getSelectedRates(),
					'align' => 'center',
					'index' => 'rate_id_child'
			));
		}
		
		$this->addColumn('rate_id_child', array(
			'header' => Mage::helper('customcatalog')->__('Rate ID'),
			'sortable' => true,
			'width' => 60,
			'index' => 'rate_id_child'
		));
		
		$this->addColumn('shape_child', array(
			'header' => Mage::helper('customcatalog')->__('Shape'),
			'width' => 100,
			'index' => 'shape_child',
			'type' => 'options',
			'options' => Dnk_Rates_Model_Shapes_Options::getOptionArray(),
		));

		$this->addColumn('size_child', array(
			'header' => Mage::helper('customcatalog')->__('Size'),
			'width' => 100,
			'index' => 'size_child',
			'type' => 'options',
			'options' => Dnk_Rates_Model_Sizes_Options::getOptionArray(),
		));
		
		/*$this->addColumn('certificate', array(
			'header' => Mage::helper('customcatalog')->__('Certificate'),
			'width' => 100,
			'index' => 'certificate',
			'type' => 'options',
			'options' => Dnk_Rates_Model_Certificates_Options::getOptionArray(),
		));
		
		$this->addColumn('rate', array(
			'header' => Mage::helper('customcatalog')->__('Rate'),
			'width' => 100,
			'index' => 'rate',
			'type' => 'number'
		));*/
		
		$this->addColumn('number', array(
			'header' => Mage::helper('customcatalog')->__('Number'),
			'name' => 'number',
			'validate_class' => 'validate-number',
			'width' => 60,
            'sortable'	=> false,
            'filter'	=> false,
			'editable' => !$this->_getProduct()->getCustomReadonly(),
			'edit_only' => !$this->_getProduct()->getId(),
			'param' => $this->_getProduct()->getRates(),
            'renderer' => 'Dnk_CustomCatalog_Block_Adminhtml_Widget_Grid_Column_Renderer_Number'
		));
		
		$this->addColumn('diamond-weight', array(
			'header' => Mage::helper('customcatalog')->__('Weight'),
			'name' => 'diamond-weight',
			'validate_class' => 'validate-number',
			'width' => 60,
            'sortable'	=> false,
            'filter'	=> false,
			'editable' => !$this->_getProduct()->getCustomReadonly(),
			'edit_only' => !$this->_getProduct()->getId(),
			'param' => $this->_getProduct()->getRates(),
            'renderer' => 'Dnk_CustomCatalog_Block_Adminhtml_Widget_Grid_Column_Renderer_Weight'
		));
				
		return parent::_prepareColumns();
	}
	
	/**
	* Rerieve grid URL
	*
	* @return string
	*/
	
	public function getGridUrl() {
		return $this->getData('grid_url')? $this->getData('grid_url'): $this->getUrl('*/*/diamondratesGrid', array('_current' => true));
	}
	
	/**
	* Retrieve selected custom products
	*
	* @return array
	*/
	
	protected function _getSelectedRates() {
		$rateIdsArray = Mage::helper('customcatalog')->getSelectedRates($this->_getProduct()->getRates());
		return $rateIdsArray;
		/*
		$products = $this->getProductsCustom();
		if (!is_array($products)) {
			$products = array_keys($this->getSelectedCustomProducts());
		}
		return $products;
		*/
	}

	/**
	* Retrieve custom products
	*
	* @return array
	*/
	
	public function getSelectedRates() {
		$productRates = Mage::registry('current_product')->getRates();
		if ($productRates != '') {
			$productRatesArray = explode(',',$productRates);
			foreach ($productRatesArray as $productRateArray) {
				$productRate = explode('-',$productRateArray);
				$productWeight = explode('|', $productRate[2]);
				$returnRate[$productRate[0]] = array('number'=>$productRate[1],'diamond-weight'=>$productWeight[0]);
			}
		}
		return $returnRate;
	}
	

}