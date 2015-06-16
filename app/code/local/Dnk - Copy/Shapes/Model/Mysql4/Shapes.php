<?php
class Dnk_Shapes_Model_Mysql4_Shapes extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("shapes/shapes", "shape_id");
    }
}