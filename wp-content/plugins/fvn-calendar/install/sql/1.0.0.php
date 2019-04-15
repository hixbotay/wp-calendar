<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}_fvn_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `invest_package_id` int(11) NOT NULL DEFAULT '0',
  `order_number` varchar(32) DEFAULT NULL,
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) DEFAULT NULL,
  `pay_method` varchar(100) NOT NULL,
  `pay_status` varchar(20) DEFAULT NULL,
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(4) NOT NULL,
  `notes` text NOT NULL,
  `ip_address` varchar(15) NOT NULL DEFAULT '',  
  `tax` varchar(45) DEFAULT NULL,
  `order_status` varchar(20) NOT NULL,
  `tx_id` varchar(100) NOT NULL,
  `deposit` decimal(15,2) DEFAULT NULL,
  `params` text NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}_fvn_invest_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) NOT NULL,
  `min_price` decimal(13,2) NOT NULL,
  `rate` text NOT NULL,
  `description` varchar(8000) NOT NULL,
  `type` varchar(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}_fvn_invest_package_daily` (
  `invest_package_id` varchar(200) NOT NULL,
  `rate` decimal(8,5) NOT NULL,
  `date` date NOT NULL,
  INDEX (`invest_package_id`),
  PRIMARY KEY (`invest_package_id`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}_fvn_draw_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invest_package_id` varchar(200) NOT NULL,
  `user_id`  int(11) NOT NULL,
  `order_id`  int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `notes` varchar(5000) NOT NULL,
  `remark` varchar(5000) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}_fvn_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invest_package_id` varchar(200) NOT NULL,
  `user_id`  int(11) NOT NULL,
  `order_id`  int(11) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `created` datetime, 
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");


