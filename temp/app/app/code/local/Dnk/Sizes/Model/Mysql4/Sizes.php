<?php
class Dnk_Sizes_Model_Mysql4_Sizes extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("sizes/sizes", "size_id");
    }
}