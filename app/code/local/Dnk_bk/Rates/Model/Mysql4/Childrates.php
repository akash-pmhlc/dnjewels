<?php
class Dnk_Rates_Model_Mysql4_Childrates extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("rates/childrates", "rate_id_child");
    }
}