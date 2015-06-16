<?php

class Dnk_Sms_Model_Overdue extends Mage_Core_Model_Config_Data
{
    public function save()
    {
        $orderid = $this->getValue(); //get the value from our config
        $model = Mage::getModel('sms/sms')->load($orderid); //strip non numeric
        Mage::log($model->getData(),null,'model.log');
        if($model['status'] == 2)   //exit if we're less than 10 digits long
        {
            Mage::getSingleton('core/session')->addNotice("Please enter sms template with status Approved for Overdue");    
        }else{
 
        return parent::save();  //call original save method so whatever happened
                                //before still happens (the value saves)
        }
    }
}