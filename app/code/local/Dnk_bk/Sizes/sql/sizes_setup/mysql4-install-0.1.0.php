<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table sizes(size_id int not null auto_increment, size varchar(100), primary key(size_id));
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 