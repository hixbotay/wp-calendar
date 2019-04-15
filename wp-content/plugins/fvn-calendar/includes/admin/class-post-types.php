<?php

// namespace HB;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Post_types Class.
 */
class HB_Post_types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( get_called_class(), 'register_taxonomies' ), 5 );
		add_action( 'init', array( get_called_class(), 'register_post_types' ), 5 );
	}

	/**
	 * Register core taxonomies.
	 */
	public static function register_taxonomies() {
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
	}

}

HB_Post_types::init();
