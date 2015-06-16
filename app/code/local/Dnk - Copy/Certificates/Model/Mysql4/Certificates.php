<?php
class Dnk_Certificates_Model_Mysql4_Certificates extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("certificates/certificates", "certificate_id");
    }
}