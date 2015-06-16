<?php
class Dnk_Rates_Model_Mysql4_Rates extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("rates/rates", "rate_id");
    }
}