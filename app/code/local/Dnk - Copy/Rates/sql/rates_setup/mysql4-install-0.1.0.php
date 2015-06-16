<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table rates(rate_id int not null auto_increment, shape varchar(100), size varchar(100), certificate varchar(100), rate varchar(100), primary key(rate_id));
create table rates_child(rate_id_child int not null auto_increment, shape_child varchar(100), size_child varchar(100) primary key(rate_id_child));		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 