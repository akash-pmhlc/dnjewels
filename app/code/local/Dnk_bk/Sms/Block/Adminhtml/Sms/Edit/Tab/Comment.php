<?php

class Dnk_Sms_Block_Adminhtml_Sms_Edit_Tab_Comment extends Mage_Adminhtml_Block_Template
{
    /**
     * Set the template for the block
     *
     */
    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('sms/comment.phtml');
    }

}