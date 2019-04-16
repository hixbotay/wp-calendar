<?php
//load include file by Url
defined ( 'ABSPATH' ) or die ();
class HB_Site_Autoload{
	
	public function __construct(){
		return true;
	}
	
	public function load(){		
		//defined action in request to execute
		add_action( 'init', array($this,'execute_action'),15);
		$this->includefiles();
		
		add_action( 'wp_enqueue_scripts', array($this,'enque_scripts'));
	}
	function enque_scripts(){
		if(HBFactory::getConfig()->get('load_bootstrap_css',1)){
			wp_enqueue_style( 'bootstrap', FVN_URL.'assets/css/bootstrap.css', '', '1.0.0' );
		}
		if(HBFactory::getConfig()->get('load_bootstrap_js',1)){
			wp_enqueue_script( 'popper', FVN_URL.'assets/js/popper.min.js', array('jquery'), '1.0.0' );
			wp_enqueue_script( 'bootstrap', FVN_URL.'assets/js/bootstrap.min.js', array('jquery'), '1.0.0' );
		}
		wp_enqueue_style( 'visa', FVN_URL.'assets/css/visa.css', '', '1.0.0' );
		wp_enqueue_script( 'hbpro-plg-js', FVN_URL.'assets/js/hbpro.js', array('jquery'), '1.0.1', true );
	}
	
	private function is_file($filename){
		return is_file(FVN_PATH.$filename);
	}
	
	private function includefiles(){
		include FVN_PATH.'includes/class-template-loader.php';
	}
	
	/**
	 * Execute admin-post function via url request
	 */
	function execute_action(){
		$input = HBFactory::getInput();
		//check offline mode
		if(HBFactory::getConfig()->get('offline_mode')){
			if(!current_user_can('manage_options')){
				if($GLOBALS['pagenow'] !='wp-login.php'){
				 wp_die('<center><h1>Website đang bảo trì, quý khách vui lòng quay lại sau!</h1></center>');
				}
			}
		}
		$view= $input->get('view');
		if($view){
			include FVN_PATH.'templates/'.$view.'.php';
			die;
		}
		$request_action = $input->get('hbaction');
		if($request_action){
			
			$task = $input->get('task');
			//Import action by request
			FvnImporter::functions($request_action);
			$class = 'FvnAction'.$request_action;
	
			$action = new $class;
			$action->execute($task);
			return;
		}
		return;
	}
	
}
