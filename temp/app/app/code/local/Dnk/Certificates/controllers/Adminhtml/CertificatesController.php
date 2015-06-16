<?php

class Dnk_Certificates_Adminhtml_CertificatesController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("certificates/certificates")->_addBreadcrumb(Mage::helper("adminhtml")->__("Certificates  Manager"),Mage::helper("adminhtml")->__("Certificates Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Certificates"));
			    $this->_title($this->__("Manager Certificates"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Certificates"));
				$this->_title($this->__("Certificates"));
			    $this->_title($this->__("Edit Certificate"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("certificates/certificates")->load($id);
				if ($model->getId()) {
					Mage::register("certificates_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("certificates/certificates");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Certificates Manager"), Mage::helper("adminhtml")->__("Certificates Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Certificates Description"), Mage::helper("adminhtml")->__("Certificates Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("certificates/adminhtml_certificates_edit"))->_addLeft($this->getLayout()->createBlock("certificates/adminhtml_certificates_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("certificates")->__("Certificate does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Certificates"));
		$this->_title($this->__("Certificates"));
		$this->_title($this->__("New Certificate"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("certificates/certificates")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("certificates_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("certificates/certificates");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Certificates Manager"), Mage::helper("adminhtml")->__("Certificates Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Certificates Description"), Mage::helper("adminhtml")->__("Certificates Description"));


		$this->_addContent($this->getLayout()->createBlock("certificates/adminhtml_certificates_edit"))->_addLeft($this->getLayout()->createBlock("certificates/adminhtml_certificates_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("certificates/certificates")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Certificate was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setCertificatesData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setCertificatesData($this->getRequest()->getPost());
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
						$model = Mage::getModel("certificates/certificates");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Certificate was successfully deleted"));
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
				$ids = $this->getRequest()->getPost('certificate_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("certificates/certificates");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Certificate(s) was successfully removed"));
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
			$fileName   = 'certificates.csv';
			$grid       = $this->getLayout()->createBlock('certificates/adminhtml_certificates_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'certificates.xml';
			$grid       = $this->getLayout()->createBlock('certificates/adminhtml_certificates_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
