<?php

class Dnk_Sms_Adminhtml_SmsController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('sms/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('sms/sms')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('sms_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('sms/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('sms/adminhtml_sms_edit'))
				->_addLeft($this->getLayout()->createBlock('sms/adminhtml_sms_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sms')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {	
                 
                 Mage::log($data,null,'data.log');
                    //echo '<pre>'; print_r($data); exit;
                    
                        $commentModel = Mage::getModel('sms/comment');
                        $adminUser = Mage::getSingleton('admin/session')->getUser();   
                        $userName = $adminUser['firstname'].' '.$adminUser['lastname'];
                        
                        if($data['status'] == ''){
                            $data['status'] = 2; // Not approved by default.
                        }

			$model = Mage::getModel('sms/sms');
                        
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}

				$model->save();
                                if($data['status'] == 1){
                                    $commentModel->setSmsTemplateId($model->getId());

                                    $commentModel->setApprovedBy($data['approved_by'])
                                                ->setAdminUser($userName)
                                                ->setComment($data['comment']);

                                    if ($commentModel->getCreatedTime() == NULL) {
                                            $commentModel->setCreatedTime(now());
                                    }

                                    $commentModel->save();
                                }


            /********* update core config value for respective sms templates **********/

                if($data['status'] == 2){

                     if($this->getRequest()->getParam('id') == 1){
                     $coreConfigObj4 = new Mage_Core_Model_Config();
                     $coreConfigObj4->saveConfig('general/sms_template/giftease_sms_order', '', 'default', 0);
                     }elseif($this->getRequest()->getParam('id') == 2){
                        $coreConfigObj4 = new Mage_Core_Model_Config();
                     $coreConfigObj4->saveConfig('general/sms_template/giftease_sms_shipment', '', 'default', 0);
                     
                     }elseif($this->getRequest()->getParam('id') == 3){
                        $coreConfigObj4 = new Mage_Core_Model_Config();
                     $coreConfigObj4->saveConfig('general/sms_template/giftease_sms_delivery', '', 'default', 0);
                     

                     }elseif($this->getRequest()->getParam('id') == 4){
                        $coreConfigObj4 = new Mage_Core_Model_Config();
                     $coreConfigObj4->saveConfig('general/sms_template/giftease_sms_cancel', '', 'default', 0);
                     

                     }elseif($this->getRequest()->getParam('id') == 5){
                        $coreConfigObj4 = new Mage_Core_Model_Config();
                     $coreConfigObj4->saveConfig('general/sms_template/giftease_sms_received', '', 'default', 0);
                     

                     }elseif($this->getRequest()->getParam('id') == 6){
                        $coreConfigObj4 = new Mage_Core_Model_Config();
                     $coreConfigObj4->saveConfig('general/sms_template/giftease_sms_processing', '', 'default', 0);
                     

                     }elseif($this->getRequest()->getParam('id') == 7){
                        $coreConfigObj4 = new Mage_Core_Model_Config();
                     $coreConfigObj4->saveConfig('general/sms_template/giftease_sms_complete', '', 'default', 0);
                     }else{

                         $coreConfigObj4 = new Mage_Core_Model_Config();
                     $coreConfigObj4->saveConfig('general/sms_template/giftease_sms_overdue', '', 'default', 0);
                     }

                  }   
                                
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sms')->__('SMS template was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
                        } catch (Exception $e) {
                            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                            Mage::getSingleton('adminhtml/session')->setFormData($data);
                            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                            return;
                        }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sms')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('sms/sms');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $smsIds = $this->getRequest()->getParam('sms');
        if(!is_array($smsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($smsIds as $smsId) {
                    $sms = Mage::getModel('sms/sms')->load($smsId);
                    $sms->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($smsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $smsIds = $this->getRequest()->getParam('sms');
        if(!is_array($smsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($smsIds as $smsId) {
                    $sms = Mage::getSingleton('sms/sms')
                        ->load($smsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($smsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'sms.csv';
        $content    = $this->getLayout()->createBlock('sms/adminhtml_sms_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'sms.xml';
        $content    = $this->getLayout()->createBlock('sms/adminhtml_sms_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}