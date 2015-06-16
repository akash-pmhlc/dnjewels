<?php

class Dnk_Rates_Adminhtml_RatesController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("rates/rates")->_addBreadcrumb(Mage::helper("adminhtml")->__("Rates  Manager"),Mage::helper("adminhtml")->__("Rates Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Rates"));
			    $this->_title($this->__("Manager Rates"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Rates"));
				$this->_title($this->__("Rates"));
			    $this->_title($this->__("Edit Rate"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("rates/rates")->load($id);
				if ($model->getId()) {
					Mage::register("rates_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("rates/rates");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Rates Manager"), Mage::helper("adminhtml")->__("Rates Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Rates Description"), Mage::helper("adminhtml")->__("Rates Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("rates/adminhtml_rates_edit"))->_addLeft($this->getLayout()->createBlock("rates/adminhtml_rates_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("rates")->__("Rate does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Rates"));
		$this->_title($this->__("Rates"));
		$this->_title($this->__("New Rate"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("rates/rates")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("rates_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("rates/rates");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Rates Manager"), Mage::helper("adminhtml")->__("Rates Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Rates Description"), Mage::helper("adminhtml")->__("Rates Description"));


		$this->_addContent($this->getLayout()->createBlock("rates/adminhtml_rates_edit"))->_addLeft($this->getLayout()->createBlock("rates/adminhtml_rates_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						$rateId = $this->getRequest()->getParam("id");
						if ($rateId == '') {
							$checkRatesExists = Mage::getModel("rates/rates")->getCollection()->addFieldToFilter('shape',$post_data['shape'])->addFieldToFilter('size',$post_data['size'])->addFieldToFilter('certificate',$post_data['certificate'])->getData();
							foreach ($checkRatesExists as $checkRateExists) {
								if(isset($checkRateExists['rate_id'])) {
									Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("Rate for the provided selections already exists"));
									Mage::getSingleton("adminhtml/session")->setRatesData($this->getRequest()->getPost());
									$this->_redirect("*/*/");
									return;
								}
							}
						}
						
						$model = Mage::getModel("rates/rates")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();


						$prevDiamondRate = Mage::getSingleton("adminhtml/session")->getPrevDiamondRate();
						Mage::getSingleton("adminhtml/session")->unsPrevDiamondRate();
						if($post_data['rate'] != $prevDiamondRate) {

							try {
								$rateId = $model->getId();
								$model = Mage::getModel("rates/rates")->load($rateId);

								$productsWithRateId = Mage::getModel('catalog/product')->getCollection()
														->addAttributeToSelect(array('listingprice','rateids','rates','entity_id','stonerates','totalweight'))
														->addAttributeToFilter('rateids', array('like' => '%'.$rateId.'%'))
														->load();
								
								foreach($productsWithRateId as $productWithRateId) {
									if($productWithRateId->getRateids() != '') {
										$productRateIds = explode(',',$productWithRateId->getRateids());
										if (in_array($rateId, $productRateIds)) {
											if($productWithRateId->getRates() != '') {
												$product = $productWithRateId;
												$price = 0;
												$productRatesArray = explode(',',$product->getRates());
												
												foreach ($productRatesArray as $productRateArray) {
													$productRate = explode('-',$productRateArray);
													$model = Mage::getModel("rates/rates")->load($productRate[0]);
													$rate = $model->getRate();
													$price += $rate*$productRate[2];
												}
												
												if($product->getStonerates() != '') {
													$productRatesArray = explode(',',$product->getStonerates());
														foreach ($productRatesArray as $productRateArray) {
															$productRate = explode('-',$productRateArray);
															$model = Mage::getModel("stone/stones")->load($productRate[0]);
															$rate = $model->getRate();
															$price += $rate*$productRate[1];
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

						$childmodel = Mage::getModel("rates/childrates")->getCollection()->addFieldToFilter('shape_child',$post_data['shape'])->addFieldToFilter('size_child',$post_data['size'])->getData();

						if (empty($childmodel)) {
							$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
							$connection->beginTransaction();
							$__fields = array();
							$__fields['shape_child'] = $post_data['shape'];
							$__fields['size_child'] = $post_data['size'];
							$__fields['rate_id_child'] = $model->getId(); 
							$connection->insert('rates_child', $__fields);
							$connection->commit();
						} 

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Rates was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setRatesData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setRatesData($this->getRequest()->getPost());
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
						$model = Mage::getModel("rates/rates");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						
						$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
						$__condition = array($connection->quoteInto('rate_id_child=?', $this->getRequest()->getParam("id")));
						$connection->delete('rates_child', $__condition);

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Rate was successfully deleted"));
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
				$ids = $this->getRequest()->getPost('rate_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("rates/rates");
					  $model->setId($id)->delete();
					  $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
					  $__condition = array($connection->quoteInto('rate_id_child=?', $id));
					  $connection->delete('rates_child', $__condition);
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Rate(s) was successfully removed"));
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
			$fileName   = 'rates.csv';
			$grid       = $this->getLayout()->createBlock('rates/adminhtml_rates_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'rates.xml';
			$grid       = $this->getLayout()->createBlock('rates/adminhtml_rates_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
		
		public function updateproductratesAction()
		{
			if( $this->getRequest()->getParam("id") > 0 ) {
				try {
					$rateId = $this->getRequest()->getParam("id");
					$model = Mage::getModel("rates/rates")->load($rateId);

					$productsWithRateId = Mage::getModel('catalog/product')->getCollection()
											->addAttributeToSelect(array('listingprice','rateids','rates','entity_id','stonerates','totalweight'))
											->addAttributeToFilter('rateids', array('like' => '%'.$rateId.'%'))
											->load();
					
					foreach($productsWithRateId as $productWithRateId) {
						if($productWithRateId->getRateids() != '') {
							$productRateIds = explode(',',$productWithRateId->getRateids());
							if (in_array($rateId, $productRateIds)) {
								if($productWithRateId->getRates() != '') {
									$product = $productWithRateId;
									$price = 0;
									$productRatesArray = explode(',',$product->getRates());
									
									foreach ($productRatesArray as $productRateArray) {
										$productRate = explode('-',$productRateArray);
										$model = Mage::getModel("rates/rates")->load($productRate[0]);
										$rate = $model->getRate();
										$price += $rate*$productRate[2];
									}
									
									if($product->getStonerates() != '') {
										$productRatesArray = explode(',',$product->getStonerates());
											foreach ($productRatesArray as $productRateArray) {
												$productRate = explode('-',$productRateArray);
												$model = Mage::getModel("stone/stones")->load($productRate[0]);
												$rate = $model->getRate();
												$price += $rate*$productRate[1];
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
