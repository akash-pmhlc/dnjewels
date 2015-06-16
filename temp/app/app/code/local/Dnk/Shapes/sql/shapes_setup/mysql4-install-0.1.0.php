<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table shapes(shape_id int not null auto_increment, shape varchar(100), primary key(shape_id));
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 