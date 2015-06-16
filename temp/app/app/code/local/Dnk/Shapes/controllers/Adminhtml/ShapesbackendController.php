<?php
class Dnk_Shapes_Adminhtml_ShapesbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Manage Shapes"));
	   $this->renderLayout();
    }
}