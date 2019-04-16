<?php
//load include file by Url
defined ( 'ABSPATH' ) or die ();
class HB_Admin_Autoload{
	
	public function __construct(){
		return true;
	}
	
	public function load(){
		if(is_file(ABSPATH.'/tmp/demo-mode.txt')){
			
		}
		//load require files when show list of post_type
		if(isset($_REQUEST['post_type'])){
			$view_name = substr($_REQUEST['post_type'], 8);
			//load list file if exist
			if($this->is_file('includes/admin/'.$view_name.'/list.php')){
				FvnImporter::includes('admin/'.$view_name.'/list');
			}
			
		}
		add_action( 'admin_enqueue_scripts', array($this,'enque_scripts'));
		add_action( 'admin_init', array($this,'execute_action'));
		add_filter( 'manage_users_columns', array($this,'new_modify_user_table') );
		add_filter( 'manage_users_custom_column', array($this,'new_modify_user_table_row'), 10, 3 );
	}

	function new_modify_user_table( $column ) {
		$column['phone'] = 'Phone';
		// $column['xyz'] = 'XYZ';
		return $column;
	}
	
	
	function new_modify_user_table_row( $val, $column_name, $user_id ) {
		switch ($column_name) {
			case 'phone' :
				return get_the_author_meta( 'phone', $user_id );
				break;
			case 'birthday' :
				return get_the_author_meta( 'birthday', $user_id );
				break;
			case 'gender' :
				return get_the_author_meta( 'gender', $user_id );
				break;
			case 'bank_number' :
				return get_the_author_meta( 'bank_number', $user_id );
				break;
			case 'bank_name' :
				return get_the_author_meta( 'bank_name', $user_id );
				break;
			case 'bank_address' :
				return get_the_author_meta( 'bank_address', $user_id );
				break;
			default:
		}
		return $val;
	}
	
	function enque_scripts(){
		wp_enqueue_style( 'bootstrap', FVN_URL.'/assets/css/bootstrap.css', '', '1.0.0' );
		wp_enqueue_script( 'bootstrap', FVN_URL.'assets/js/bootstrap.min.js', array('jquery'), '1.0.0' );
		wp_enqueue_script( 'hbpro-plg-js', FVN_URL.'assets/js/hbpro.js', array('jquery'), '1.0.0', true );
	}
	
	public function is_file($filename){
		return is_file(FVN_PATH.$filename);
	}
	function execute_action(){
		
		$input = HBFactory::getInput();
		$request_action = $input->get('fvnaction');
	
		//$user = wp_get_current_user();
		//debug($user);die;
		$task = $input->get('task');
		if($request_action && $task){
			$meta_nonce = $input->get('hb_meta_nonce');
			if ( empty( $meta_nonce ) || ! wp_verify_nonce( $input->get('hb_meta_nonce'), 'hb_action' ) ) {
				//@TODO check nonce
			}
			//Import action by request
			FvnImporter::viewaction($request_action);
			$class = 'FvnAction'.$request_action;
			$action = new $class;
			$action->execute($task);
			exit;
		}
		return;
	}
	
	
	
}
