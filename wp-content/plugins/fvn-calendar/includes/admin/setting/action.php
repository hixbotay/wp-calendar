<?php
class FvnActionSetting extends FvnAction{	
	
	public function save(){
		update_option('hb_params', json_encode($_POST['params']));
		HB_enqueue_message(__('Save success!','hb'));
		wp_redirect(admin_url('admin.php?page=setting'));
		return;
	}
	
	/**
	 * Save setting of gateway
	 */
	public function savegateway(){
		$gateway = $this->input->getString('gateway');
		update_option($gateway, json_encode($_POST['params']));
		hb_enqueue_message(__('Save success!','hb'));
		wp_redirect(admin_url("admin.php?page=setting&layout=checkout&gateway={$gateway}"));
		return;
	}
	
	function set_role(){
		
		$role_object = get_role( 'editor' );
		$role_object->add_cap( 'edit_theme_options' );
		wp_redirect(admin_url("admin.php?page=setting&layout=role"));
		return;
	}
	
	function saveMail(){
		$data = $this->input->get('data');
		$template = $this->input->get('template');
		$key = 'fvn_mail_'.$template;
		$data = $_POST['data'];
		update_option($key, json_encode($data));		
		wp_redirect(admin_url("admin.php?page=setting&layout=notify&template=".$template));
		exit;;
	}
	
}