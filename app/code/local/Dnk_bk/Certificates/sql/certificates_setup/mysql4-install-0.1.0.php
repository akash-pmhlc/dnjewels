<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table certificates(certificate_id int not null auto_increment, certificate varchar(100), primary key(certificate_id));
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 