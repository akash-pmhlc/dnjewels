<?php

class Dnk_Sizes_Adminhtml_SizesController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("sizes/sizes")->_addBreadcrumb(Mage::helper("adminhtml")->__("Sizes  Manager"),Mage::helper("adminhtml")->__("Sizes Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Sizes"));
			    $this->_title($this->__("Manager Sizes"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Sizes"));
				$this->_title($this->__("Sizes"));
			    $this->_title($this->__("Edit Size"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("sizes/sizes")->load($id);
				if ($model->getId()) {
					Mage::register("sizes_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("sizes/sizes");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Sizes Manager"), Mage::helper("adminhtml")->__("Sizes Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Sizes Description"), Mage::helper("adminhtml")->__("Sizes Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("sizes/adminhtml_sizes_edit"))->_addLeft($this->getLayout()->createBlock("sizes/adminhtml_sizes_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("sizes")->__("Size does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Sizes"));
		$this->_title($this->__("Sizes"));
		$this->_title($this->__("New Size"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("sizes/sizes")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("sizes_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("sizes/sizes");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Sizes Manager"), Mage::helper("adminhtml")->__("Sizes Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Sizes Description"), Mage::helper("adminhtml")->__("Sizes Description"));


		$this->_addContent($this->getLayout()->createBlock("sizes/adminhtml_sizes_edit"))->_addLeft($this->getLayout()->createBlock("sizes/adminhtml_sizes_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("sizes/sizes")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Size was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setSizesData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setSizesData($this->getRequest()->getPost());
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
						$model = Mage::getModel("sizes/sizes");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Size was successfully deleted"));
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
				$ids = $this->getRequest()->getPost('size_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("sizes/sizes");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Size(s) was successfully removed"));
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
			$fileName   = 'sizes.csv';
			$grid       = $this->getLayout()->createBlock('sizes/adminhtml_sizes_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'sizes.xml';
			$grid       = $this->getLayout()->createBlock('sizes/adminhtml_sizes_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
