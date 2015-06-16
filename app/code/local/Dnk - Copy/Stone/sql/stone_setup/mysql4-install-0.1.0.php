<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table stones(stone_id int not null auto_increment, name varchar(100), rate varchar(100), primary key(stone_id));
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 