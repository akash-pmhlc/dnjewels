<?php

class Dnk_Sendsms_Model_Observer extends Mage_Core_Model_Abstract
{

	public function sendShipmentSmsToCustomer(Varien_Event_Observer $observer){

		try {
			
			$shipment = $observer->getEvent()->getShipment();
            $order = $shipment->getOrder();
            $mobilenumber = $order->getBillingAddress()->getTelephone();

            if($mobilenumber) {
            	
            	$shipmentconfigValue = Mage::getStoreConfig('sales_email/shipment/enabled',$order->getStoreId());

   			    if($shipmentconfigValue == 1) {
			 
			 		$shipping_address = $order->getShippingAddress();
			 

			 		$shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
				    	->setOrderFilter($order)
				    	->load();

					foreach ($shipmentCollection as $shipment) {
	            		// This will give me the shipment IncrementId, but not the actual tracking information.
	            		foreach($shipment->getAllTracks() as $tracknum) {
	            	
	                		$tracknums[]= $tracknum->getNumber();
	                		$courier[] = $tracknum->getTitle();
	            		}
	            		
	            		//Mage::log($shipment->getAllTracks()->getData(),null,'courier-outer.log');

	        		}
					
					$smsTemplateIdForShipment = Mage::getStoreConfig('general/sms_template/sms_shipment');
	         		
	         		$model = Mage::getModel('sms/sms')->load($smsTemplateIdForShipment);
                    $content = $model->getSmsTemplateContent();
                    $couriername = $courier[0];
                    $tracking = $tracknums[0];
                    $arr = explode("/", strrev($tracking), 2);
                    $tracking = strrev($arr[0]);
			 		
			 		$smsvariables = array('0'=>"".$order->getIncrementId()."",'1'=>"".$couriername."",'2'=>"".$tracking."");
			 		
			 		$smscontent = Mage::helper('sendsms')->getSmsContent($content,$event='shipment_confirm',$smsvariables);

				    $workingKey = Mage::getStoreConfig('general/service_provider/app_secret_key');
				    $smsServiceUrl = Mage::getStoreConfig('general/service_provider/sms_service_url');

				    $countryId = $order->getBillingAddress()->getCountryId();
					$countryDialingCode = Mage::helper('sendsms')->getCountryDialingCode($countryId);
					    
				    $sendSmsUrl = $smsServiceUrl.'workingkey='.$workingKey.'&to='.$countryDialingCode.$mobilenumber.'&sender=SAMONI&message='.urlencode($smscontent);

				    //echo "URL - ".$sendSmsUrl;
Mage::log($sendSmsUrl,null,'response-admin-order.log');
				    $ch = curl_init();

					// set url
					curl_setopt($ch, CURLOPT_URL, $sendSmsUrl);

					//return the transfer as a string
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

					// $response contains the output string
					$response = curl_exec($ch);
					        
					if (curl_errno($ch)) {
					    $sad = curl_error($ch);
					    throw new Exception($sad);
					}

					// close curl resource to free up system resources
					curl_close($ch);
					
					$result = json_decode($response, TRUE);
					Mage::log($response,null,'response-admin-order.log');

				}
				  
			}
	  
		} catch(Exception $e) {

			//Admin notification
			Mage::log($e->getMessage(),null,'sms-error.log');
		}	


	}


	public function sendSmsToCustomerForOrderPlace(Varien_Event_Observer $observer) {

		try {	
		
			//$order = $observer->getEvent()->getOrder();
			
			$order_id = $observer->getData('order_ids');
			$order = Mage::getModel('sales/order')->load($order_id);
   
			$mobilenumber = $order->getBillingAddress()->getTelephone();
				
			//$order = Mage::getModel('sales/order')->load($order->getId());

			if($mobilenumber) {
				
				$salesconfigValue = Mage::getStoreConfig('sales_email/order/enabled',$order->getStoreId());

				if($salesconfigValue == 1) {
				 
				    //if($order->getEmailSent() == 1) {
						
						$smsTemplateIdForOrder = Mage::getStoreConfig('general/sms_template/sms_order');
    					
    					$model = Mage::getModel('sms/sms')->load($smsTemplateIdForOrder);
    					$content = $model->getSmsTemplateContent();

    					//$customerCareNumber = Mage::getStoreConfig('general/customer_care_number/number');

					    $smsvariables = array('0'=>"".$order->getBillingAddress()->getFirstname().' '.$order->getBillingAddress()->getLastname()."",'1'=>"".$order->getIncrementId()."");                         
					    $smscontent = Mage::helper('sendsms')->getSmsContent($content,$event='order_confirm',$smsvariables);

					    $workingKey = Mage::getStoreConfig('general/service_provider/app_secret_key');
					    $smsServiceUrl = Mage::getStoreConfig('general/service_provider/sms_service_url');

					    $countryId = $order->getBillingAddress()->getCountryId();
    					$countryDialingCode = Mage::helper('sendsms')->getCountryDialingCode($countryId);
						    
					    $sendSmsUrl = $smsServiceUrl.'workingkey='.$workingKey.'&to='.$countryDialingCode.$mobilenumber.'&sender=&message='.urlencode($smscontent);

					    //echo "URL - ".$sendSmsUrl;
Mage::log($sendSmsUrl,null,'response-admin-order.log');
					    $ch = curl_init();

						// set url
						curl_setopt($ch, CURLOPT_URL, $sendSmsUrl);

						//return the transfer as a string
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

						// $response contains the output string
						$response = curl_exec($ch);
						        
						if (curl_errno($ch)) {
						    $sad = curl_error($ch);
						    throw new Exception($sad);
						}

						// close curl resource to free up system resources
						curl_close($ch);
						
						$result = json_decode($response, TRUE);
						Mage::log($response,null,'response-admin-order.log');
		
		
					//}
				}
				 
			}  
			  
		} catch(Exception $e) {

			//Admin notification
			Mage::log($e->getMessage(),null,'sms-error.log');
		}	


	}

	
public function sendOrderCancelSmsToCustomer(Varien_Event_Observer $observer) {

  try { 
  
   $order = $observer->getEvent()->getOrder();
  
   $mobilenumber = $order->getBillingAddress()->getTelephone();
    
   //$order = Mage::getModel('sales/order')->load($order->getId());

   if($mobilenumber) {
    
    $salesconfigValue = Mage::getStoreConfig('sales_email/order/enabled',$order->getStoreId());

    if($salesconfigValue == 1) {
     
        if($order->getEmailSent() == 1) {
      
      $smsTemplateIdForOrder = Mage::getStoreConfig('general/sms_template/sms_order_cancel');
         
         $model = Mage::getModel('sms/sms')->load($smsTemplateIdForOrder);
         $content = $model->getSmsTemplateContent();

         //$customerCareNumber = Mage::getStoreConfig('general/customer_care_number/number');

         $smsvariables = array('0'=>"".$order->getBillingAddress()->getFirstname().' '.$order->getBillingAddress()->getLastname()."",'1'=>"".$order->getIncrementId()."");                         
         $smscontent = Mage::helper('sendsms')->getSmsContent($content,$event='order_cancel',$smsvariables);

         $workingKey = Mage::getStoreConfig('general/service_provider/app_secret_key');
         $smsServiceUrl = Mage::getStoreConfig('general/service_provider/sms_service_url');

         $countryId = $order->getBillingAddress()->getCountryId();
         $countryDialingCode = Mage::helper('sendsms')->getCountryDialingCode($countryId);
          
         $sendSmsUrl = $smsServiceUrl.'workingkey='.$workingKey.'&to='.$countryDialingCode.$mobilenumber.'&sender=SAMONI&message='.urlencode($smscontent);

         //echo "URL - ".$sendSmsUrl;

         $ch = curl_init();

      // set url
      curl_setopt($ch, CURLOPT_URL, $sendSmsUrl);

      //return the transfer as a string
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      // $response contains the output string
      $response = curl_exec($ch);
              
      if (curl_errno($ch)) {
          $sad = curl_error($ch);
          throw new Exception($sad);
      }

      // close curl resource to free up system resources
      curl_close($ch);
      
      $result = json_decode($response, TRUE);
      Mage::log($response,null,'response-admin-order.log');
  
  
     }
    }
     
   }  
     
  } catch(Exception $e) {

   //Admin notification
   Mage::log($e->getMessage(),null,'sms-error.log');
  } 


 }	
}

