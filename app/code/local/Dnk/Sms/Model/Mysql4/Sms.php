<?php

class Dnk_Sms_Model_Mysql4_Sms extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the sms_template_id refers to the key field in your database table.
        $this->_init('sms/sms', 'sms_template_id');
    }
}