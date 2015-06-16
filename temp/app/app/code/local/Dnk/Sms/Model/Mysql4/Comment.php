<?php

class Dnk_Sms_Model_Mysql4_Comment extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the comment_id refers to the key field in your database table.
        $this->_init('sms/comment', 'comment_id');
    }
}