<?php

class Dnk_Stone_Adminhtml_StonesController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("stone/stones")->_addBreadcrumb(Mage::helper("adminhtml")->__("Stones  Manager"),Mage::helper("adminhtml")->__("Stones Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Stone"));
			    $this->_title($this->__("Manager Stones"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Stone"));
				$this->_title($this->__("Stones"));
			    $this->_title($this->__("Edit Stone"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("stone/stones")->load($id);
				if ($model->getId()) {
					Mage::register("stones_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("stone/stones");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Stones Manager"), Mage::helper("adminhtml")->__("Stones Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Stones Description"), Mage::helper("adminhtml")->__("Stones Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("stone/adminhtml_stones_edit"))->_addLeft($this->getLayout()->createBlock("stone/adminhtml_stones_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("stone")->__("Stone does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Stone"));
		$this->_title($this->__("Stones"));
		$this->_title($this->__("New Stone"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("stone/stones")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("stones_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("stone/stones");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Stones Manager"), Mage::helper("adminhtml")->__("Stones Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Stones Description"), Mage::helper("adminhtml")->__("Stones Description"));


		$this->_addContent($this->getLayout()->createBlock("stone/adminhtml_stones_edit"))->_addLeft($this->getLayout()->createBlock("stone/adminhtml_stones_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						$model = Mage::getModel("stone/stones")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						$prevStoneRate = Mage::getSingleton("adminhtml/session")->getPrevStonesRate();
						Mage::getSingleton("adminhtml/session")->unsPrevStonesRate();
						if($post_data['rate'] != $prevStoneRate){

							try {
								$stoneId = $model->getId();
								$model = Mage::getModel("stone/stones")->load($stoneId);

								$productsWithStoneId = Mage::getModel('catalog/product')->getCollection()
														->addAttributeToSelect(array('price','stoneids','stonerates','entity_id','stonecharge','taxrate','listingprice'))
														->addAttributeToFilter('stoneids', array('like' => '%'.$stoneId.'%'))
														->load();
								
								
								foreach($productsWithStoneId as $productWithStoneId) {
									if($productWithStoneId->getStoneids() != '') {
										$productStoneIds = explode(',',$productWithStoneId->getStoneids());
										if (in_array($stoneId, $productStoneIds)) {
											if($productWithStoneId->getStonerates() != '') {
												$currentStoneCharge = $productWithStoneId->getStonecharge();
												$product = $productWithStoneId;
												$price = 0;
												$productStoneRatesArray = explode(',',$product->getStonerates());
												
												$searchString = $stoneId.'-';
												$input = preg_quote($searchString, '~'); // don't forget to quote input string!
												$result = preg_grep('~' . $input . '~', $productStoneRatesArray);
												$resultArray = explode('-', $result[0]);
												$stoneWeightForProduct = $resultArray[1];

												$currentStonePrevRate = $stoneWeightForProduct*$prevStoneRate;
												$currentStoneNewRate = $stoneWeightForProduct*$post_data['rate'];
												
												$productWithStoneId->setStonecharge($currentStoneCharge-$currentStonePrevRate+$currentStoneNewRate);
												$listingPrice = $productWithStoneId->getListingprice() - $currentStoneCharge + $productWithStoneId->getStonecharge();


												$taxRate = Mage::getStoreConfig('dnktax/taxrate/rate');
												if($taxRate > 0) $productWithStoneId->setTaxrate(($listingPrice-$productWithStoneId->getTaxrate())*$taxRate/100);
												
												if($listingPrice > 0) $productWithStoneId->setListingprice(round($listingPrice+$productWithStoneId->getTaxrate()));
											
												$productWithStoneId->save();

											}
										}
									}
								}
								
							} 
							catch (Exception $e) {
								Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
								//$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
							}

							
						}


						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Product prices were successfully updated"));
								
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Stones was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setStonesData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setStonesData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("stone/stones");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Stone was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('stone_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("stone/stones");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Stone(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'stones.csv';
			$grid       = $this->getLayout()->createBlock('stone/adminhtml_stones_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'stones.xml';
			$grid       = $this->getLayout()->createBlock('stone/adminhtml_stones_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
		
		public function updateproductratesAction()
		{
			if( $this->getRequest()->getParam("id") > 0 ) {
				try {
					$stoneId = $this->getRequest()->getParam("id");
					$model = Mage::getModel("stone/stones")->load($stoneId);

					$productsWithStoneId = Mage::getModel('catalog/product')->getCollection()
											->addAttributeToSelect(array('price','stoneids','stonerates','entity_id','stonecharge','taxrate','listingprice'))
											->addAttributeToFilter('stoneids', array('like' => '%'.$stoneId.'%'))
											->load();
					
					foreach($productsWithStoneId as $productWithStoneId) {
						if($productWithStoneId->getStoneids() != '') {
							$productStoneIds = explode(',',$productWithStoneId->getStoneids());
							if (in_array($stoneId, $productStoneIds)) {
								if($productWithStoneId->getStonerates() != '') {
									$product = $productWithStoneId;
									$price = 0;
									$productStoneRatesArray = explode(',',$product->getStonerates());
									
									foreach ($productStoneRatesArray as $productStoneRateArray) {
										$productRate = explode('-',$productStoneRateArray);
										$model = Mage::getModel("stone/stones")->load($productRate[0]);
										$rate = $model->getRate();
										$price += $rate*$productRate[1];
									}
									
									if($product->getRates() != '') {
										$productRatesArray = explode(',',$product->getRates());
											foreach ($productRatesArray as $productRateArray) {
												$productRate = explode('-',$productRateArray);
												$model = Mage::getModel("rates/rates")->load($productRate[0]);
												$rate = $model->getRate();
												$price += $rate*$productRate[2];
										}
									}
									
									$totalWeight = $product->getTotalweight();
									if($price > Mage::getStoreConfig('labor/laborrate/threshold')) {
										$laborRate = Mage::getStoreConfig('labor/laborrate/morethanthreshold');
										if(($totalWeight > 0) && ($laborRate > 0)) $product->setLabor($totalWeight*$laborRate);
									} else {
										$laborRate = Mage::getStoreConfig('labor/laborrate/lessthanthreshold');
										if(($totalWeight > 0) && ($laborRate > 0)) $product->setLabor($laborRate);
									}

									if($price > 0) $product->setPrice($price+$product->getLabor());
									
									$product->save();
								}
							}
						}
					}
					
					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Product prices were successfully updated"));
					$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
				} 
				catch (Exception $e) {
					Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
					$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
				}
			}
			$this->_redirect("*/*/");
		}
}
