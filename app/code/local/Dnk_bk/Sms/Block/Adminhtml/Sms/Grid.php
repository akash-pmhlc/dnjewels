<?php

class Dnk_Sms_Block_Adminhtml_Sms_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('smsGrid');
      $this->setDefaultSort('sms_template_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('sms/sms')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('sms_template_id', array(
          'header'    => Mage::helper('sms')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'sms_template_id',
      ));

      $this->addColumn('sms_template_name', array(
          'header'    => Mage::helper('sms')->__('SMS Template Name'),
          'align'     =>'left',
          'width'     => '150px',
          'index'     => 'sms_template_name',
      ));

      $this->addColumn('sms_template_content', array(
          'header'    => Mage::helper('sms')->__('SMS Template Content'),
          'index'     => 'sms_template_content',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('sms')->__('Approved Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Approved',
              2 => 'Not approved',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('sms')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('sms')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('sms')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('sms')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('sms_template_id');
        $this->getMassactionBlock()->setFormFieldName('sms');

        if(Mage::getSingleton('admin/session')->isAllowed('homepage/sms/approved'))
        {
            $this->getMassactionBlock()->addItem('delete', array(
                 'label'    => Mage::helper('sms')->__('Delete'),
                 'url'      => $this->getUrl('*/*/massDelete'),
                 'confirm'  => Mage::helper('sms')->__('Are you sure?')
            ));
        

            $statuses = Mage::getSingleton('sms/status')->getOptionArray();

            array_unshift($statuses, array('label'=>'', 'value'=>''));
            $this->getMassactionBlock()->addItem('status', array(
                 'label'=> Mage::helper('sms')->__('Change status'),
                 'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                 'additional' => array(
                        'visibility' => array(
                             'name' => 'status',
                             'type' => 'select',
                             'class' => 'required-entry',
                             'label' => Mage::helper('sms')->__('Status'),
                             'values' => $statuses
                         )
                 )
            ));
        }
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}