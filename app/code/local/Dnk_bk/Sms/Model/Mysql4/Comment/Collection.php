<?php

class Dnk_Sms_Model_Mysql4_Comment_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('sms/comment');
    }
}