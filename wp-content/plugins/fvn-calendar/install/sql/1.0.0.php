<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
$wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}_fvn_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `order_number` varchar(32) DEFAULT NULL,
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) DEFAULT NULL,
  `pay_method` tinyint(1) NOT NULL,
  `pay_status` tinyint(1) DEFAULT NULL,
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
  `start_time` varchar(5) NOT NULL,
  `end` date NOT NULL,
  `end_time` varchar(5) NOT NULL,
  `type_desc` varchar(300) NOT NULL,
  `name` varchar(400) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(256) NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `birthday` date NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

