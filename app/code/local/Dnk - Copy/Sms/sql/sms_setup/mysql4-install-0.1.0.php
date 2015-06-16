<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('sms')};
CREATE TABLE {$this->getTable('sms')} (
  `sms_template_id` int(11) unsigned NOT NULL auto_increment,
  `sms_template_name` varchar(255) NOT NULL default '',
  `sms_template_content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`sms_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('sms_comment_history')} (
  `comment_id` int(11) unsigned NOT NULL auto_increment,
  `sms_template_id` int(11) unsigned NOT NULL default '0',
  `approved_by`  varchar(255) NOT NULL default '',
  `admin_user`  varchar(255) NOT NULL default '',
  `comment` text NOT NULL default '',
  `created_time` datetime NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 