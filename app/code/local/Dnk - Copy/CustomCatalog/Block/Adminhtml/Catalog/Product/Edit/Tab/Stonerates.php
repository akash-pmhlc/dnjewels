<?php

class Dnk_CustomCatalog_Block_Adminhtml_Catalog_Product_Edit_Tab_Stonerates extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	* Set grid params
	*
	*/
	public function __construct() {

		parent::__construct();
		$this->setId('stonerates_grid');
		$this->setDefaultSort('stone_id');
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
		if ($column->getId() == 'stoneids_in_product') {
			$stoneIds = $this->_getSelectedStoneRates();
			if (empty($stoneIds)) {
				$stoneIds = 0;
			}
		
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('stone_id', array('in' => $stoneIds));
			} else {
				if($stoneIds) {
					$this->getCollection()->addFieldToFilter('stone_id', array('nin' => $stoneIds));
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
		$collection = Mage::getModel('stone/stones')->getCollection();
			//->getProductCollection()
			//->setProduct($this->_getProduct())
			//->addAttributeToSelect('*');

		if ($this->isReadonly()) {
			$stoneIds = $this->_getSelectedStoneRates();
			if (empty($stoneIds)) {
				$stoneIds = array(0);
			}
			$collection->addFieldToFilter('stone_id', array('in' => $stoneIds));
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
			$this->addColumn('stoneids_in_product', array(
					'header_css_class' => 'a-center',
					'type' => 'checkbox',
					'name' => 'stoneids_in_product',
					'values' => $this->_getSelectedStoneRates(),
					'align' => 'center',
					'index' => 'stone_id'
			));
		}
		
		$this->addColumn('stone_id', array(
			'header' => Mage::helper('customcatalog')->__('Stone ID'),
			'sortable' => true,
			'width' => 60,
			'index' => 'stone_id'
		));
		
		$this->addColumn('name', array(
			'header' => Mage::helper('customcatalog')->__('Stone Name'),
			'width' => 100,
			'index' => 'name',
			'type' => 'text'
		));

		$this->addColumn('rate', array(
			'header' => Mage::helper('customcatalog')->__('Rate'),
			'width' => 100,
			'index' => 'rate',
			'type' => 'number'
		));
		
		
		$this->addColumn('stone-weight', array(
			'header' => Mage::helper('customcatalog')->__('Weight'),
			'name' => 'stone-weight',
			'validate_class' => 'validate-number',
			'width' => 60,
            'sortable'	=> false,
            'filter'	=> false,
			'editable' => !$this->_getProduct()->getCustomReadonly(),
			'edit_only' => !$this->_getProduct()->getId(),
			'param' => $this->_getProduct()->getStonerates(),
            'renderer' => 'Dnk_CustomCatalog_Block_Adminhtml_Widget_Grid_Column_Renderer_StoneWeight'
		));
				
		return parent::_prepareColumns();
	}
	
	/**
	* Rerieve grid URL
	*
	* @return string
	*/
	
	public function getGridUrl() {
		return $this->getData('grid_url')? $this->getData('grid_url'): $this->getUrl('*/*/stoneratesGrid', array('_current' => true));
	}
	
	/**
	* Retrieve selected custom products
	*
	* @return array
	*/
	
	protected function _getSelectedStoneRates() {
		$stoneIdsArray = Mage::helper('customcatalog')->getSelectedStoneRates($this->_getProduct()->getStonerates());
		return $stoneIdsArray;
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
	
	public function getSelectedStoneRates() {
		$productRates = Mage::registry('current_product')->getStonerates();
		if ($productRates != '') {
			$productRatesArray = explode(',',$productRates);
			foreach ($productRatesArray as $productRateArray) {
				$productRate = explode('-',$productRateArray);
				$returnRate[$productRate[0]] = array('stone-weight'=>$productRate[1]);
			}
		}
		return $returnRate;
	}
	

}