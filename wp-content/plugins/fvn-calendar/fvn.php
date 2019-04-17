<?php
/*
Plugin Name: Đặt lịch	
Plugin URI: http://freelancerviet.net
Description: Đặt lịch hẹn.
Version:  1.0.0
Author: Freelancerviet.net
Author URI: http://freelancerviet.net/
Text Domain: fvn
License: GPLv2
*/

defined( 'ABSPATH' ) or die( 'Restricted access' );
// die('dfdf');
if ( ! class_exists( 'HBFactory' ) ) :
class HBFactory {
	public $version = '1.0.0';
	public $loader;
	//configuration of plugin
	static $config;
	//process input of request
	static $input;
	//session
	static $cart;
	static $user;
	/**
	 * The single instance of the class.
	 *
	 */
	
	protected static $_instance = null;
	
	/**
	 * main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		$this->includes();	
		$this->hook();
	}
	
	private function includes(){
		//define construct
		require 'defines.php';
		//libraries
		require_once FVN_PATH.'libraries/hbobject.php';
		require_once FVN_PATH.'libraries/importer.php';
		require_once FVN_PATH.'libraries/hbview.php';
		require_once FVN_PATH.'libraries/fvnaction.php';
		require_once FVN_PATH.'libraries/table.php';
		require_once FVN_PATH.'libraries/model.php';
		
		FvnImporter::helper('debug','html','list','helper');
		FvnImporter::includes('functions','widget-functions');
		FvnImporter::params('base','orderstatus','paystatus','videocall');
		//import require file only for admin sie
		if(is_admin()){
			FvnImporter::includes(
					'admin/class-menu',
					'admin/class-post-types',
					'admin/functions',
					'admin/class-admin-autoloader',
					'admin/class-admin-metaboxs',
					'admin/class-view/class-posttype-view-list',
					'admin/class-view/class-posttype-view-metabox'
					);
			HB_Menu::addMenu();
			//add style
			add_action( 'admin_enqueue_scripts', 'hb_admin_add_root_stylesheet' );
			//load require file when need to optimize memory
			$this->loader = new HB_Admin_Autoload();
			$this->loader->load();
		}else{
			FvnImporter::includes(
					'class-site-autoloader'
					);
			//load require file when need to optimize memory
			$this->loader = new HB_Site_Autoload();
			$this->loader->load();	
		}
		
		//add extensions
		$config = self::getConfig();
		if(isset($config->app_smtp_mail)&&$config->app_smtp_mail){
			include_once FVN_PATH.'apps/wp_mail_smtp.php';
		}
		if(isset($config->app_vn_wc)&&$config->app_vn_wc){
			include_once FVN_PATH.'apps/woocommerce-vn.php';
		}
		if(isset($config->app_estate)&&$config->app_estate){
			include_once FVN_PATH.'apps/estate.php';
		}
		
		
	}
	
	
	//get configuration of plugin
	public static function getConfig($name=''){
		if(empty(self::$config)){
			$config = get_option('hb_params','{}');
			$config = json_decode($config);
			//set default value if it is not set
			$default = array('date_format_short'=>'Y-m-d',
					'date_format_type_long'=>'Y-m-d',
					'formatHours'  => 12,
					'currency_symbol' => '$',
					'currency_display' => '0',
					'main_currency'	=> 'USD',
					'ps_group' => 1,
					'currency_decimalpoint' => 2,
					'facebook_comment' => 0
			);
			foreach ($default as $key=>$val){
				if(!isset($config->$key)){
					$config->$key = $val;
				}
			}
			$config = new HBObject($config); 
			self::$config = $config;
		}
		if($name){
			return self::$config->$name;
		}
		return self::$config;
	}
	
	
	public static function getDate($string_date = 'now',$timezone= false){
		if($timezone){
			$date = new DateTime($string_date, new DateTimeZone($timezone));
		}else{
			$date = new DateTime($string_date);
		}
	
		return $date;
	}
	
	/*
	 * Get input of request
	 */
	public static function getInput(){
		if(empty(self::$input)){
			require_once FVN_PATH.'libraries/factory/hbinput.php';
			self::$input = new HBInput();
		}
		return self::$input;
	}
	
	/**
	 * get cart from session
	 */
	public static function getCart(){
		if(empty(self::$cart)){
			FvnImporter::includes('class-cart');
			self::$cart = new HBCart();
			self::$cart->load();
		}
		return self::$cart;
	}
	
	/**
	 * Get account logined
	 * @return Customer
	 */
	public static function getUser($user_id = null)
	{
		$id = (int)$user_id;
		if(!self::$user[$id]){
			require_once FVN_PATH.'libraries/user.php';
			if($id){
				self::$user[$id] = new FvnUser($id);
			}else{
				$user = wp_get_current_user();
				if($user->ID){
					$id = $user->ID;
					if(!self::$user[$user->ID]){
						self::$user[$user->ID] = new FvnUser($user->ID);
					}
				}
			}			
		}
		return self::$user[$id];
	}
	public static function getQuery(){
		require_once 'libraries/model/query.php';
		return (new FvnModelQuery());
	}
	
	public function hook(){
		include FVN_PATH.'includes/hook/post.php';
	}
	
}
endif;

HBFactory::instance();


//install path
function fvn_active_plugin() {
	require FVN_PATH.'install/install.php';
	return;
}

function fvn_deactive_plugin()
{
	return;
}
register_activation_hook( __FILE__, 'fvn_active_plugin' );
register_deactivation_hook( __FILE__, "fvn_deactive_plugin");