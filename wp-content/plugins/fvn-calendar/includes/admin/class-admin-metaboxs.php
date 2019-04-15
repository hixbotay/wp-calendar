<?php

/**
 * add metaboxs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class HB_Admin_Meta_Boxes {

	public $meta_box_errors=array();
	
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );	

		//add extra title of edit form
		add_action( 'edit_form_top', array($this,'post_type_title_edit') );
		// Error handling (for showing errors from meta boxes on next page load)
// 		add_action( 'admin_notices', array( $this, 'output_errors' ) );
// 		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}
	
	//edit title of edit form
	function post_type_title_edit( $post ) {
		switch ($post->post_type){
			case 'HB_route':
				if($post->post_title){
					echo "<a class='button' href='".admin_url('admin.php?page=HB_rate&route_id='.$post->ID)."' id='my-custom-header-link'>".__('Add Rate','hb')."</a>";					
				}
				break;
			default:
				return;				
		}
	}

	/**
	 * Add an error message.
	 * @param string $text
	 */
	

	/**
	 * Show any stored error messages.
	 */
	public function output_errors($error) {
			echo '<div id="HB_errors" class="error notice is-dismissible">';

			echo $error;

			echo '</div>';
		
	}

	/**
	 * Add WC Meta boxes.
	 */
	public function add_meta_boxes() {	
		$screen = get_current_screen();
		//auto import metabox file
// 		debug(strpos('dest', $screen->post_type));die;
		if(strpos('hb', $screen->post_type)==0){
			$view_name = substr($screen->post_type, 3);
			if(is_file(FVN_PATH.'includes/admin/'.$view_name.'/metabox.php')){
				HBImporter::metabox($view_name);
			}
		}

	}


	/**
	 * Remove bloat.
	 */
	public function remove_meta_boxes() {
// 		remove_meta_box( 'postexcerpt', 'product', 'normal' );
		return;
	}


	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		if(in_array($post->post_type,array('post','page','category'))){
			//clear vietnamese character in post link
			$post->post_name = FvnHelper::translate_eng(urldecode($post->post_name));
			global $wpdb;
			$wpdb->update($wpdb->posts, array('post_name'=>$post->post_name), array('ID'=>$post->ID));
		}
		// Check the nonce
		if ( empty( $_POST['hb_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hb_meta_nonce'], 'HB_save_data' ) ) {
			return;
		}
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post )) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		
		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// import metabox and save
		$view_name = substr($post->post_type, 8);
		HBImporter::metabox($view_name);
		$class = 'HBView'.$view_name.'_Metabox';
		//$func = 'save_'.$post->post_type;
		$check = $class::save( $post_id, $post );
		return true;
	}
	

}

new HB_Admin_Meta_Boxes();
