<?php

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

 -- DROP TABLE IF EXISTS {$this->getTable('googletrustedstores/log')};
 
  CREATE TABLE IF NOT EXISTS {$this->getTable('googletrustedstores/log')} (
      `id` int(11) unsigned NOT NULL auto_increment,
      `order_id` varchar(255) NOT NULL default '',
      `status` varchar(255) NOT NULL default '',
      `cancel_reason` varchar(255) NOT NULL default '',
      `shipment_title` varchar(255) NOT NULL default '',
      `track_number` varchar(255) NOT NULL default '',
      `created_at` timestamp NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->getConnection()->addColumn($this->getTable('sales/order'), 'gts_exported', 'tinyint(1) unsigned NOT NULL default 0');


$installer->endSetup();