<?php

class Dnk_Sms_Model_Sms extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('sms/sms');
    }
}