<?php

class Dnk_Rates_Model_Certificates_Options extends Mage_Core_Model_Abstract
{
    public function getOptionArray(){

  $certificates = Mage::getModel('certificates/certificates')->getCollection()->getData();
    
  $certificatesArray = array();
  foreach($certificates as $certificate) {
   $certificatesArray[$certificate['certificate_id']] = $certificate['certificate'];    
  }
  
  return $certificatesArray;
    }

}