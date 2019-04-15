<?php 
if (!defined("WP_UNINSTALL_PLUGIN")) 
exit();

global $wpdb;
$wpdb->get_results("DROP TABLE IF EXISTS `{$wpdb->prefix}_fvn_orders`");
$wpdb->get_results("DROP TABLE IF EXISTS `{$wpdb->prefix}_fvn_invest_package`");
$wpdb->get_results("DROP TABLE IF EXISTS `{$wpdb->prefix}_fvn_invest_package_daily`");
$wpdb->get_results("DROP TABLE IF EXISTS `{$wpdb->prefix}_fvn_draw_request`");
$wpdb->get_results("DROP TABLE IF EXISTS `{$wpdb->prefix}_fvn_transaction`");


