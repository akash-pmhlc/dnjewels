<?php
class Dnk_Stone_Model_Mysql4_Stones extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("stone/stones", "stone_id");
    }
}