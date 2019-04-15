<?php
/**
 * @package 	FVN-extension
 * @author 		Joombooking
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined('ABSPATH') or die('Restricted access');

class FvnActionUser extends FvnAction{
	
	
	//register user
	function register(){ 
		global $wpdb;
		//check nonce
		if ( empty( $_POST['hb_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hb_meta_nonce'], 'hb_action' ) ) {
			wp_die('invalid request');
		}
		//check captcha
		//save user
		$post = $this->input->getPost();
		//$post['user']['user_login'] = $post['user']['user_email'];
		$post['user']['user_login'] = $post['user']['phone'];
		$user_name = $post['user']['user_login'];
		$user_email = $post['user']['user_email'];
		$user_id = username_exists( $user_name );
		if(!$user_id){
			$user_id = $check_email = email_exists($user_email);
		}
		$filter_array_user_data = array('user_login','user_pass','display_name','user_email');
		
		if ( !$user_id) {	
			HBImporter::helper('math');
			$userdata = FvnHelperMath::filterArray($post['user'], $filter_array_user_data);
			$user_id = wp_insert_user( $userdata ) ;
			
		} else {
			if($check_email){
				hb_enqueue_message('Email đã được sử dụng ở một tài khoản khác, vui lòng thử một email khác!','danger');
			}else{
				hb_enqueue_message('Tên tài khoản đã bị trùng, vui lòng thử một tên khác','danger');
			}
			wp_safe_redirect(site_url('register-page'));
			exit;
		}
		if($user_id){
			//add user meta
			$user_meta = FvnHelperMath::filterArray($post['user'], array('phone','birthday','address','gender','bank_name','bank_number','bank_address'));
			
			foreach ($user_meta as $key=>$value){
				add_user_meta( $user_id, $key, $value);
			}
			hb_enqueue_message('Chúc mừng bạn đã đăng kí tài khoản thành công');
			$creds = array(
					'user_login'    => $user_name,
					'user_password' => $post['user']['user_pass'],
					'remember'		=> 1
			);
			wp_signon( $creds, false );
			wp_safe_redirect(site_url('profile'));	
			exit;
		}
		//send email
		wp_safe_redirect(site_url('register-page'));
		exit;
	}
	
	function update(){ 
		global $wpdb;
		//check nonce
		if ( empty( $_POST['hb_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hb_meta_nonce'], 'hb_action' ) ) {
			wp_die('invalid request');
		}
		HBImporter::helper('math');
		//check captcha
		//save user
		$post = $this->input->getPost();
		FvnHelper::checkLogin();
		$user = HBFactory::getUser();
		$user_data = [
			'ID'=>$user->id,
			'display_name' => $post['data']['display_name'],
		];
		if(!$user->email){
			if(email_exists($post['data']['email'])){
				hb_enqueue_message('Email đã bị tồn tại','error');
				wp_safe_redirect(site_url('/profile'));
				exit;
			}
			$user_data['user_email'] = $post['data']['email'];
		}
		if($post['data']['password']){
			if($post['data']['password']!=$post['data']['confirm_password']){
				hb_enqueue_message('Xác nhận mật khẩu không trùng khớp','error');
				wp_safe_redirect(site_url('/profile'));
				exit;
			}
			$user_data['user_pass'] = $post['data']['password'];
		}
		if (wp_update_user($user_data)) {
			$user_meta = FvnHelperMath::filterArray($post['data'], array('phone','birthday','address','gender','bank_name','bank_number','bank_address'));			
			foreach ($user_meta as $key=>$value){
				update_user_meta( $user->id, $key, $value);
			}
			hb_enqueue_message('Cập nhật thành công');
		}else{
			hb_enqueue_message('Cập nhật không thành công','error');
		}
		
		//send email
		wp_safe_redirect(site_url('/profile'));
		exit;
	}
	
	
	function login(){ 
	
		//check nonce
		if ( empty( $_POST['hb_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hb_meta_nonce'], 'hb_action' ) ) {
			wp_die('invalid request');
		}
		$data = $this->input->get('data');
		$creds = array(
					'user_login'    => $data['email'],
					'user_password' => $data['password'],
					'remember'		=> $data['remember']
			);
		$user = wp_signon( $creds,is_ssl() );
		if($user->ID){
			if($this->input->getString('redirect')){
				wp_redirect(base64_decode($this->input->getString('redirect')));
			}else{
				wp_redirect(site_url('/profile'));
			}
			
		}else{
			wp_redirect(site_url('/login-page'));
			hb_enqueue_message('Sai tên đăng nhập hoặc mật khẩu','error');
		}
		exit;
		
	}
	
	function resetpassword(){
		//check nonce
		$meta_nonce = $this->input->get('hb_meta_nonce');
		if ( empty( $meta_nonce ) || ! wp_verify_nonce( $meta_nonce, 'hb_action' ) ) {			
			wp_safe_redirect('trang-khong-ton-tai');
			exit;
		}
		$this->display('resetpassword');		
		exit;
	}
	
	function sendResetPasswordMail(){
		//check nonce
		$meta_nonce = $this->input->get('hb_meta_nonce');
		if ( empty( $meta_nonce ) || ! wp_verify_nonce( $meta_nonce, 'hb_action' ) ) {
			wp_safe_redirect('trang-khong-ton-tai');
			exit;
		}
		//check user email
		$email = $this->input->getString('user_mail');
		$user = get_user_by( 'email', $email );
		if(!$user){
			wp_safe_redirect('dang-nhap');
			hb_enqueue_message('Email không đúng hoặc người dùng không tồn tại!','error');
			exit;
		}
		//send mail		 
		$key = get_password_reset_key( $user );
		$url_confirm = site_url("index.php?fvnaction=user&task=reset_password_confirm&key={$key}&login=$email");
		$subject = "WOAFUN reset mật khẩu tài khoản';
		$body = 'Xin chào,<br>
		Bạn vừa chọn chức năng cài đặt lại mật khẩu, vui lòng truy cập đường dẫn dưới đây để cài đặt lại mật khẩu của bạn!<br>
		<a href='$url_confirm'>$url_confirm</a>";
		
		FvnHelper::sendMail($email,$subject,$body);
		
		wp_safe_redirect('dang-nhap');
		hb_enqueue_message('Vui lòng kiểm tra email của bạn để tạo lại mật khẩu của bạn!','success');
		exit;
	}
	
	function reset_password_confirm(){
		$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
		if ( isset( $_GET['key'] ) ) {
			$value = sprintf( '%s:%s', wp_unslash( $_GET['login'] ), wp_unslash( $_GET['key'] ) );
			setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
			wp_safe_redirect( remove_query_arg( array( 'key', 'login' ) ) );
			exit;
		}
		
		if ( isset( $_COOKIE[ $rp_cookie ] ) && 0 < strpos( $_COOKIE[ $rp_cookie ], ':' ) ) {
			list( $rp_login, $rp_key ) = explode( ':', wp_unslash( $_COOKIE[ $rp_cookie ] ), 2 );
			$user = check_password_reset_key( $rp_key, $rp_login );
			if ( isset( $_POST['pass1'] ) && ! hash_equals( $rp_key, $_POST['rp_key'] ) ) {
				$user = false;
			}
		} else {
			$user = false;
		}
		
		if ( ! $user || is_wp_error( $user ) ) {
			setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
			if ( $user && $user->get_error_code() === 'expired_key' )
				wp_redirect( site_url( 'wp-login.php?action=lostpassword&error=expiredkey' ) );
				else
					wp_redirect( site_url( 'wp-login.php?action=lostpassword&error=invalidkey' ) );
					exit;
		}
		
		$errors = new WP_Error();
		
		if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] )
			$errors->add( 'password_reset_mismatch', __( 'The passwords do not match.' ) );
		
			/**
			 * Fires before the password reset procedure is validated.
			 *
			 * @since 3.5.0
			 *
			 * @param object           $errors WP Error object.
			 * @param WP_User|WP_Error $user   WP_User object if the login and reset key match. WP_Error object otherwise.
			 */
			do_action( 'validate_password_reset', $errors, $user );
		
			if ( ( ! $errors->get_error_code() ) && isset( $_POST['pass1'] ) && !empty( $_POST['pass1'] ) ) {
				reset_password($user, $_POST['pass1']);
				setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
				login_header( __( 'Password Reset' ), '<p class="message reset-pass">' . __( 'Your password has been reset.' ) . ' <a href="' . esc_url( wp_login_url() ) . '">' . __( 'Log in' ) . '</a></p>' );
				login_footer();
				exit;
			}
		
			wp_enqueue_script('utils');
			wp_enqueue_script('user-profile');
		
			login_header(__('Reset Password'), '<p class="message reset-pass">' . __('Enter your new password below.') . '</p>', $errors );
	}
	
	
	function registercoupon(){		
		/*
		if ( empty( $_POST['hb_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hb_meta_nonce'], 'hb_action' ) ) {
			echo json_encode(false);exit;
		}
		*/
		$email = $this->input->getString('email');
		
		global $wpdb;
		
		if(!$wpdb->get_results("select email from {$wpdb->prefix}hbpro_register_email where email LIKE '{$email}'")){
			$data['email'] = $email;
			$wpdb->insert("{$wpdb->prefix}hbpro_register_email", $data);
		}
		
		hb_enqueue_message('Chúc mừng bạn đã đăng kí thành công!');
		wp_safe_redirect(site_url('thong-bao'));
		exit;
	}
	
	private function send_notify($data){
		$config = HBFactory::getConfig();
		//send email;
		$email_subject = site_url().__(' có đăng kí mới');
		$email_body = '<b>'.site_url().'</b>';
		if($data['phone']){
			$email_body .= "<p>Phone: {$data['phone']}</p>";
			
			//send sms	
			/*
			$msg = site_url();
			HBImporter::apps('sms_clickatell/clickatell');
			$check = Hbpro_sms_clickatell::send(array('sms'=>$msg,'phone' => $config->phone));
			*/
		}
		if($data['email']){
			$email_body .= "<p>email: {$data['email']}</p>";					
		}
		if($data['notes']){
			$email_body .= "<p>Ghi chú: {$data['notes']}</p>";					
		}
		$headers = array('Content-Type: text/html; charset=UTF-8');	
		
		$check = wp_mail($config->email, $email_subject, $email_body, $headers, '');
		
		return true;
	}
	
	function ajax_register_phone(){
	/*
		if ( empty( $_POST['hb_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hb_meta_nonce'], 'hb_action' ) ) {
			echo json_encode(false);exit;
		}
		*/
		HBImporter::libraries('model');
		$model = new FvnModel('#__hbpro_register_email','id');
		$phone = trim($this->input->getString('phone'));
				
		if(empty($phone)){
			echo json_encode(array('status'=>'0'));exit;
		}
		
		global $wpdb;
		$data = array();
		$data['phone'] = $phone;
		$data['checked'] = 0;
		$check = $model->save($data);
		$this->send_notify($data);
		echo json_encode(array('status'=>$check));exit;
	}
	
	
	
	function ajax_register_email(){
		HBImporter::libraries('model');
		$model = new FvnModel('#__hbpro_register_email','id');
		$email = trim($this->input->getString('email'));		
		if(empty($email)){
			echo json_encode(array('status'=>'0'));exit;
		}
		$data = array();
		$data['email'] = $email;
		$data['checked'] = 0;
		$check = $model->save($data);
		$this->send_notify($data);
		
		$attachments = array(WP_CONTENT_DIR . '/uploads/file_to_attach.zip');
		$headers = array('Content-Type: text/html; charset=UTF-8');
		$config = HBFactory::getConfig();
		$email_subject = 'Eurowindowriverpark.info thân gửi anh(chị) báo giá hợp đồng';
		$email_body = '';
		wp_mail($config->email, $email_subject, $email_body, $headers, $attachments);
		echo json_encode(array('status'=>$check));exit;
	}
	
	
	
	function ajax_register(){
		HBImporter::libraries('model');
		$model = new FvnModel('#__hbpro_register_email','id');
		$email = trim($this->input->getString('email'));	
		$phone = trim($this->input->getString('phone'));
		
				
		if(empty($email) && empty($phone)){
			echo json_encode(array('status'=>'0'));exit;
		}
		
		$data = array();
		$data['email'] = $email;
		$data['phone'] = $phone;
		$data['notes'] = $this->input->getString('notes');
		$data['name'] = $this->input->getString('name');
		$data['checked'] = 0;
		$check = $model->save($data);
		
		$this->send_notify($data);
		echo json_encode(array('status'=>$check));exit;
	}

	function logout(){
		wp_logout();
		hb_enqueue_message('Đăng xuất thành công');
		wp_redirect(site_url('login-page'));
		exit;
	}
	
	
}