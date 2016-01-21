<?php


class m_action extends class_model {

var $tables = array(
  'actions' => "
  `id` bigint(20) NOT NULL auto_increment,
  `code` varchar(255) null,
  `time` int(11) not null default '0',
  `ip` varchar(255) null,
  `agent` varchar(255) null,
  `refer` varchar(255) null,
  
  `value` varchar(255) null,
  
  `created_at` datetime default NULL,
  `created_by` int(11) NOT NULL default '0',
  `updated_at` datetime default NULL,
  `updated_by` int(11) NOT NULL default '0',
  `expires_at` datetime default NULL,
  
  PRIMARY KEY  (`id`),
  KEY (`code`, `time`, `ip`),
  KEY (`code`, `time`, `ip`, `agent`),
  KEY (`code`, `ip`),
  KEY (`code`)
  "
);

function m_action() {
  return $this->__construct();
}

function __construct() {
  global $sv;  
  
  $this->t = $sv->t['actions'];
  
}



//eoc
}

?>