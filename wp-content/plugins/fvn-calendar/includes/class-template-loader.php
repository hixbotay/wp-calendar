<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Template Loader
 * Load template and override from theme for front end
 * @class 		HB_Template_Loader
 * 
 */
class HB_Template_Loader
{

	/**
	 * Hook in methods.
	 */
	public static function init()
	{
		add_filter('template_include', array(__CLASS__, 'template_loader'));
	}

	/**
	 * Load a template.
	 *
	 * For override template of current theme. It is "HB" folder in theme. If there are no existed file in folder
	 * "HB" in theme then it will use file in template of plugin
	 *
	 * @param mixed $template
	 * @return string
	 */
	public static function template_loader($template)
	{
		global $wpdb;
		$file = '';
		$url = FvnHelper::get_url_path();
// debug($url);die;

		switch ($url[0]) {
			
			case 'payment';
				$file = 'payment.php';
				break;
			case 'myorders':
				$file = 'user-orders.php';
				break;
			case 'orderdetail':
				$file = 'user-order-detail.php';
				break;			
			case 'profile':
				$file = 'user-profile.php';
				break;
			case 'login-page':
				$file = 'user-login.php';
				break;
			case 'register-page':
				$file = 'user-register.php';				
				break;
			case 'booking':
				$file = 'booking.php';
				break;
			case 'message':
				$file = 'message.php';
				break;
		}

		if ($file) {
			$find = self::getRoot($file);
			$template = $find;
		}
		return $template;
	}

	public static function getRoot($file_name)
	{
		$path = get_template_directory() . '/hbpro/' . $file_name;
		if (file_exists($path)) {
			return $path;
		} else {
			return plugin_dir_path(__DIR__) . 'templates/' . $file_name;
		}
	}
}

HB_Template_Loader::init();
