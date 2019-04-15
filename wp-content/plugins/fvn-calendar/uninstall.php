<?php 
if (!defined("WP_UNINSTALL_PLUGIN")) 
exit();

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}_fvn_orders`");
$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}_fvn_invest_package`");
$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}_fvn_invest_package_daily`");
$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}_fvn_draw_request`");
$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}_fvn_transaction`");


