<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table rates(rate_id int not null auto_increment, shape varchar(100), size varchar(100), rate varchar(100), primary key(rate_id));
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 