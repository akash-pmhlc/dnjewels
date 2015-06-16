<?php

class Dnk_Sendsms_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function sendOrderconfirmSmsToCustomer($orderid){
		
		$order = Mage::getModel('sales/order')->load($orderid);
		try {	
		
			$mobilenumber = $order->getBillingAddress()->getTelephone();
				
			//$order = Mage::getModel('sales/order')->load($order->getId());

			if($mobilenumber) {
				
				$salesconfigValue = Mage::getStoreConfig('sales_email/order/enabled',$order->getStoreId());

				if($salesconfigValue == 1) {
				 
				    if($order->getEmailSent() == 1) {
						
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

	public function sendOrderCancelSms($orderid) {

		try{
				 $order = Mage::getModel('sales/order')->load($orderid);
				 //$shipping_address = $order->getShippingAddress();
				 $mobilenumber = $order->getOrderUpdatesSmsNumber();
				 
				 if($mobilenumber){
				 $smstemplateid = Mage::getStoreConfig('general/sms_template/dnk_sms_cancel');
				 $model = Mage::getModel('sms/sms')->load(4);
		         
				 $content = $model->getSmsTemplateContent();
				 $carenumber = explode("{",$content);
				 $custcareno = explode("}",$carenumber[2]);
				 $smsvariables = array('0'=>"".$order->getIncrementId()."",'1'=>"".$custcareno[0]."");
				 
				 $smscontent = $this->getSmsContent($content,$event='order_cancel',$smsvariables);
				 //$content = "Your order ".$orderincrementid."is cancelled. Do check your email for details.";
				 Mage::log($smscontent,null,'smscontent-cancelorder.log');
				 //if($payment_method_code != "cashondelivery"){

					 	 $kf_authurl = 'http://www.myvaluefirst.com/smpp/sendsms?';
						 $data = array('username' => 'gttechcri','password' => 'gttech04',
						 	'to' => $mobilenumber,'from' => 'GIFTEZ','text' => $smscontent,'drl-mask' => 19);
						  //$r = array('key' => $key , 'command' => $command, 'hash' => $hash,'var1' => $var1);

						$qs = http_build_query($data);

						//$wsUrl = "https://info.payu.in/merchant/postservice?form=1";
						$c = curl_init();
						curl_setopt($c, CURLOPT_URL, $kf_authurl);
						curl_setopt($c, CURLOPT_POST, 1);
						curl_setopt($c, CURLOPT_POSTFIELDS, $qs);
						curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
						curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
						$response = curl_exec($c);

						if (curl_errno($c)) {
						  $sad = curl_error($c);
						  throw new Exception($sad);
						}
						curl_close($c);

						$result = json_decode($response, TRUE);
						Mage::log($response,null,'response-cancel-order.log');


				 }
				 
				  
			  
		}catch(Exception $e){

			//Admin notification
			Mage::log($e->getMessage(),null,'sms-error.log');
		}	


	}


	public function sendRmaStatusSms($rmaid,$rma_order_incrementid,$rma_orderid,$rmastatus,$type){

		try{
				 $order = Mage::getModel('sales/order')->load($rma_orderid);
				 //$shipping_address = $order->getShippingAddress();
				 $mobilenumber = $order->getOrderUpdatesSmsNumber();
				 
				 if($mobilenumber){

				 if($rmastatus == 'pending'){

				 	$smstemplateid = Mage::getStoreConfig('general/sms_template/dnk_sms_received');
				 	$model = Mage::getModel('sms/sms')->load($smstemplateid);

				 	$content = $model->getSmsTemplateContent();
				 	$smsvariables = array('0'=>"".$order->getIncrementId()."");
				 	$smscontent = $this->getSmsContent($content,$event='rma_received',$smsvariables);

				 }elseif($rmastatus == 'authorized'){

				 	$smstemplateid = Mage::getStoreConfig('general/sms_template/dnk_sms_processing');
				 	$model = Mage::getModel('sms/sms')->load($smstemplateid);

				 	$content = $model->getSmsTemplateContent();
				 	$smsvariables = array('0'=>"".$order->getIncrementId()."");
				 	$smscontent = $this->getSmsContent($content,$event='rma_processing',$smsvariables);

				 }else{

				 	 $rma = Mage::registry('current_rma');
		        	 /** @var $collection Enterprise_Rma_Model_Resource_Item_Collection */
			         $collection = $rma->getItemsForDisplay();
			         //$collection->getSelect()->join('sales_flat_order_item','sales_flat_order_item.item_id = e.order_item_id',array('sales_flat_order_item.row_total','sales_flat_order_item.discount_amount')'sales_flat_order_item.row_total','sales_flat_order_item.discount_amount');
			         $collection->getSelect()->join(array('order_item'=> 'sales_flat_order_item'),'order_item.item_id = e.order_item_id', array('order_item.row_total','order_item.discount_amount'));
			         
			         $refund = $collection->getData();
			         

			         foreach($refund as $refunddata){

			         	$refund = $refunddata['row_total']-$refunddata['discount_amount'];
			         	$refund_amount = number_format(round($refund, 2), 2);

			         }
			         
			         

					$smstemplateid = Mage::getStoreConfig('general/sms_template/dnk_sms_complete');
				 	$model = Mage::getModel('sms/sms')->load($smstemplateid);
				 	$content = $model->getSmsTemplateContent();	
				 	
			 		$smsvariables = array('0'=>$refund_amount,'1'=>"".$order->getIncrementId()."");	
			 		$smscontent = $this->getSmsContent($content,$event='rma_complete',$smsvariables);
				 		
				 }

				 //$smstemplateid = Mage::getStoreConfig('general/sms_template/rma_status');
		         //$model = Mage::getModel('sms/sms')->load($smstemplateid);
		         /*if($type == 4){

		         	$request_type = "Refund";
		         }else{

		         	$request_type = "Store Credit";
		         }*/
		         
				 //$content = $model->getSmsTemplateContent();
				 //$smsvariables = array('0'=>"".$request_type."",'1'=>"".$order->getIncrementId()."",
				 	//'2'=>"".$rmastatus."");
				 //$smscontent = $this->getSmsContent($content,$event='rma_status',$smsvariables);
				 //$content = "Your RMA no".$rmaid." for order ".$rma_order_incrementid." is ".$rmastatus.". Do check your email for details.";

				 //if($payment_method_code != "cashondelivery"){

					 	 $kf_authurl = 'http://www.myvaluefirst.com/smpp/sendsms?';
						 $data = array('username' => 'gttechcri','password' => 'gttech04',
						 	'to' => $mobilenumber,'from' => 'GIFTEZ','text' => $smscontent,'drl-mask' => 19);
						  //$r = array('key' => $key , 'command' => $command, 'hash' => $hash,'var1' => $var1);

						$qs = http_build_query($data);

						//$wsUrl = "https://info.payu.in/merchant/postservice?form=1";
						$c = curl_init();
						curl_setopt($c, CURLOPT_URL, $kf_authurl);
						curl_setopt($c, CURLOPT_POST, 1);
						curl_setopt($c, CURLOPT_POSTFIELDS, $qs);
						curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
						curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
						$response = curl_exec($c);

						if (curl_errno($c)) {
						  $sad = curl_error($c);
						  throw new Exception($sad);
						}
						curl_close($c);

						$result = json_decode($response, TRUE);
						Mage::log($response,null,'response-rma.log');


				 }
				 
				  
			  
		}catch(Exception $e){

			//Admin notification
			Mage::log($e->getMessage(),null,'sms-error-rma.log');
		}	


	}


	public function sendDeliveryConfirmationSms($orderid,$shipment){

		try{
				 $order = Mage::getModel('sales/order')->load($orderid);
				 //$shipping_address = $order->getShippingAddress();
				 $mobilenumber = $order->getOrderUpdatesSmsNumber();
				 
				 if($mobilenumber){

				 $smstemplateid = Mage::getStoreConfig('general/sms_template/dnk_sms_delivery');
				 $model = Mage::getModel('sms/sms')->load($smstemplateid);
		         $content = $model->getSmsTemplateContent();

		          /******* get delivery date here *******/
		          $date = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
			 	  $day =  Mage::helper('core')->formatDate($date, 'medium', false);
				  $fday = str_replace( ',', '', $day );
				  $dday = explode(" ",$fday);
				  $finalday = implode("-",$dday);
				  $delivery_date = explode("-",$finalday);

				  $temp = $delivery_date[0];
				  $delivery_date[0] = $delivery_date[1];
				  $delivery_date[1] = $temp;

				  $deliveryday = implode("-",$delivery_date);

				   /******* get delivery date here *******/

				  $smsvariables = array('0'=>"".$order->getIncrementId()."",'1'=>"".$deliveryday."");
				 						
				 $smscontent = $this->getSmsContent($content,$event='delivery_confirm',$smsvariables);
				 
				 //$content = "Thank you for shopping with giftease.com.Your order ".$order->getIncrementId()."has been successfully delivered on ".$order->getDeliveryDateFormated()." ";

				 //if($payment_method_code != "cashondelivery"){

					 	 $kf_authurl = 'http://www.myvaluefirst.com/smpp/sendsms?';
						 $data = array('username' => 'gttechcri','password' => 'gttech04',
						 	'to' => $mobilenumber,'from' => 'GIFTEZ','text' => $smscontent,'drl-mask' => 19);
						  //$r = array('key' => $key , 'command' => $command, 'hash' => $hash,'var1' => $var1);

						$qs = http_build_query($data);

						//$wsUrl = "https://info.payu.in/merchant/postservice?form=1";
						$c = curl_init();
						curl_setopt($c, CURLOPT_URL, $kf_authurl);
						curl_setopt($c, CURLOPT_POST, 1);
						curl_setopt($c, CURLOPT_POSTFIELDS, $qs);
						curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 60);
						curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
						$response = curl_exec($c);

						if (curl_errno($c)) {
						  $sad = curl_error($c);
						  throw new Exception($sad);
						}
						curl_close($c);

						$result = json_decode($response, TRUE);
						Mage::log($response,null,'response-deliveryconfirm.log');


				 }
				 
				  
			  
		}catch(Exception $e){

			//Admin notification
			Mage::log($e->getMessage(),null,'sms-error.log');
		}	


	}

	public function getSmsContent($string,$evt,$smsvariables){

			//$str = 'The {quick} brown fox jumps {overthe} lazy dog where {noone} comes';		
			$result = $this->parseCurlyBrace($string);
                        
			$data = array();

			if($evt == 'order_confirm'){

				for($i=0;$i<count($result);$i++){
				$finalstr = str_replace($result[$i],$smsvariables[$i],$string) ;
				$string = $finalstr;
				}
				$finalstr = preg_replace('/[{}]/', '', $string);
				
			}

			if($evt == 'shipment_confirm'){

				for($i=0;$i<count($result);$i++){
				$finalstr = str_replace($result[$i],$smsvariables[$i],$string) ;
				$string = $finalstr;
				}
				$finalstr = preg_replace('/[{}]/', '', $string);
				
			}

			if($evt == 'delivery_confirm'){

				for($i=0;$i<count($result);$i++){
				$finalstr = str_replace($result[$i],$smsvariables[$i],$string) ;
				$string = $finalstr;
				}
				$finalstr = preg_replace('/[{}]/', '', $string);
				
			}

			if($evt == 'order_cancel'){

				for($i=0;$i<count($result);$i++){
				$finalstr = str_replace($result[$i],$smsvariables[$i],$string) ;
				$string = $finalstr;
				}
				$finalstr = preg_replace('/[{}]/', '', $string);
				
			}

			if($evt == 'rma_received'){

				for($i=0;$i<count($result);$i++){
				$finalstr = str_replace($result[$i],$smsvariables[$i],$string) ;
				$string = $finalstr;
				}
				$finalstr = preg_replace('/[{}]/', '', $string);

			}

			if($evt == 'rma_processing'){

				for($i=0;$i<count($result);$i++){
				$finalstr = str_replace($result[$i],$smsvariables[$i],$string) ;
				$string = $finalstr;
				}
				$finalstr = preg_replace('/[{}]/', '', $string);

			}

			
			if($evt == 'rma_complete'){

				for($i=0;$i<count($result);$i++){
				$finalstr = str_replace($result[$i],$smsvariables[$i],$string) ;
				$string = $finalstr;
				}
				$finalstr = preg_replace('/[{}]/', '', $string);

			}

			
                        
            if($evt == 'order_overduesms') {

				for($i=0;$i<count($result);$i++){
				$finalstr = str_replace($result[$i],$smsvariables[$i],$string) ;
				$string = $finalstr;
				}
				$finalstr = preg_replace('/[{}]/', '', $string);

			}
			
			return $finalstr;
	}

	public function parseCurlyBrace($str){

			  
			  $length = strlen($str);
			  $stack  = array();
			  $result = array();

			  for($i=0; $i < $length; $i++) {

			     if($str[$i] == '{') {
			        $stack[] = $i;
			     }

			     if($str[$i] == '}') {
			        $open = array_pop($stack);
			        $result[] = substr($str,$open+1, $i-$open-1);
			        
			      }
			  }

			  return $result;
	}
        
        public function sendOverdueSms($mobileNumber,$orderID) {

		try{
                    $smstemplateid = Mage::getStoreConfig('general/sms_template/dnk_sms_overdue');
                    $model = Mage::getModel('sms/sms')->load($smstemplateid);
		         
                    $content = $model->getSmsTemplateContent();
		    $smsvariables = array('0'=>"".$orderID."");		 
                    $smscontent = $this->getSmsContent($content,$event='order_overduesms',$smsvariables);
                    
                    $kf_authurl = 'http://www.myvaluefirst.com/smpp/sendsms?';
                    $data = array('username' => 'gttechcri','password' => 'gttech04',
                            'to' => $mobileNumber,'from' => 'GIFTEZ','text' => $smscontent,'drl-mask' => 19);
                    
                    $qs = http_build_query($data);
                    $c = curl_init();
                    curl_setopt($c, CURLOPT_URL, $kf_authurl);
                    curl_setopt($c, CURLOPT_POST, 1);
                    curl_setopt($c, CURLOPT_POSTFIELDS, $qs);
                    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 60);
                    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
                    $response = curl_exec($c);
                    
                    if (curl_errno($c)) {
                        $sad = curl_error($c);
			throw new Exception($sad);
                    }
                    curl_close($c);

                    $result = json_decode($response, TRUE);
                    return $response;
		}
                catch(Exception $e){
			Mage::log($e->getMessage(),null,'sms-overdue-error.log');
		}
	}

	public function getCountryDialingCode($countryId)
	{
		$countryCodesArray = array( 'IL' => '972', 'AF' => '93', 'AL' => '355', 'DZ' => '213', 'AS' => '1684', 'AD' => '376', 'AO' => '244', 'AI' => '1264', 'AG' => '1268', 'AR' => '54', 'AM' => '374', 'AW' => '297', 'AU' => '61', 'AT' => '43', 'AZ' => '994', 'BS' => '1242', 'BH' => '973', 'BD' => '880', 'BB' => '1246', 'BY' => '375', 'BE' => '32', 'BZ' => '501', 'BJ' => '229', 'BM' => '1441', 'BT' => '975', 'BA' => '387', 'BW' => '267', 'BR' => '55', 'IO' => '246', 'BG' => '359', 'BF' => '226', 'BI' => '257', 'KH' => '855', 'CM' => '237', 'CA' => '1', 'CV' => '238', 'KY' => '345', 'CF' => '236', 'TD' => '235', 'CL' => '56', 'CN' => '86', 'CX' => '61', 'CO' => '57', 'KM' => '269', 'CG' => '242', 'CK' => '682', 'CR' => '506', 'HR' => '385', 'CU' => '53', 'CY' => '537', 'CZ' => '420', 'DK' => '45', 'DJ' => '253', 'DM' => '1767', 'DO' => '1849', 'EC' => '593', 'EG' => '20', 'SV' => '503', 'GQ' => '240', 'ER' => '291', 'EE' => '372', 'ET' => '251', 'FO' => '298', 'FJ' => '679', 'FI' => '358', 'FR' => '33', 'GF' => '594', 'PF' => '689', 'GA' => '241', 'GM' => '220', 'GE' => '995', 'DE' => '49', 'GH' => '233', 'GI' => '350', 'GR' => '30', 'GL' => '299', 'GD' => '1473', 'GP' => '590', 'GU' => '1671', 'GT' => '502', 'GN' => '224', 'GW' => '245', 'GY' => '595', 'HT' => '509', 'HN' => '504', 'HU' => '36', 'IS' => '354', 'IN' => '91', 'ID' => '62', 'IQ' => '964', 'IE' => '353', 'IT' => '39', 'JM' => '1876', 'JP' => '81', 'JO' => '962', 'KZ' => '77', 'KE' => '254', 'KI' => '686', 'KW' => '965', 'KG' => '996', 'LV' => '371', 'LB' => '961', 'LS' => '266', 'LR' => '231', 'LI' => '423', 'LT' => '370', 'LU' => '352', 'MG' => '261', 'MW' => '265', 'MY' => '60', 'MV' => '960', 'ML' => '223', 'MT' => '356', 'MH' => '692', 'MQ' => '596', 'MR' => '222', 'MU' => '230', 'YT' => '262', 'MX' => '52', 'MC' => '377', 'MN' => '976', 'ME' => '382', 'MS' => '1664', 'MA' => '212', 'MM' => '95', 'NA' => '264', 'NR' => '674', 'NP' => '977', 'NL' => '31', 'AN' => '599', 'NC' => '687', 'NZ' => '64', 'NI' => '505', 'NE' => '227', 'NG' => '234', 'NU' => '683', 'NF' => '672', 'MP' => '1670', 'NO' => '47', 'OM' => '968', 'PK' => '92', 'PW' => '680', 'PA' => '507', 'PG' => '675', 'PY' => '595', 'PE' => '51', 'PH' => '63', 'PL' => '48', 'PT' => '351', 'PR' => '1939', 'QA' => '974', 'RO' => '40', 'RW' => '250', 'WS' => '685', 'SM' => '378', 'SA' => '966', 'SN' => '221', 'RS' => '381', 'SC' => '248', 'SL' => '232', 'SG' => '65', 'SK' => '421', 'SI' => '386', 'SB' => '677', 'ZA' => '27', 'GS' => '500', 'ES' => '34', 'LK' => '94', 'SD' => '249', 'SR' => '597', 'SZ' => '268', 'SE' => '46', 'CH' => '41', 'TJ' => '992', 'TH' => '66', 'TG' => '228', 'TK' => '690', 'TO' => '676', 'TT' => '1868', 'TN' => '216', 'TR' => '90', 'TM' => '993', 'TC' => '1649', 'TV' => '688', 'UG' => '256', 'UA' => '380', 'AE' => '971', 'GB' => '44', 'US' => '1', 'UY' => '598', 'UZ' => '998', 'VU' => '678', 'WF' => '681', 'YE' => '967', 'ZM' => '260', 'ZW' => '263', 'BO' => '591', 'BN' => '673', 'CC' => '61', 'CD' => '243', 'CI' => '225', 'FK' => '500', 'GG' => '44', 'VA' => '379', 'HK' => '852', 'IR' => '98', 'IM' => '44', 'JE' => '44', 'KP' => '850', 'KR' => '82', 'LA' => '856', 'LY' => '218', 'MO' => '853', 'MK' => '389', 'FM' => '691', 'MD' => '373', 'MZ' => '258', 'PS' => '970', 'PN' => '872', 'RE' => '262', 'RU' => '7', 'BL' => '590', 'SH' => '290', 'KN' => '1869', 'LC' => '1758', 'MF' => '590', 'PM' => '508', 'VC' => '1784', 'ST' => '239', 'SO' => '252', 'SJ' => '47', 'SY' => '963', 'TW' => '886', 'TZ' => '255', 'TL' => '670', 'VE' => '58', 'VN' => '84', 'VG' => '1284', 'VI' => '1340');
		
		return $countryCodesArray[$countryId];
	}
}
	 