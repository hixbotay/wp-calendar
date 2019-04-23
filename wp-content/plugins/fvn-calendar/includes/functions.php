<?php

add_action('wp_after_admin_bar_render', 'hb_render_msg', 15, 2);
add_action('wp_footer', 'hb_render_msg', 15, 2);

$config = HBFactory::getConfig();

//display message in session message error
function hb_render_msg(){
	if(isset($_SESSION['hb']['message'])){
		if($_SESSION['hb']['message']){
			if(is_admin()){
				foreach ($_SESSION['hb']['message'] as $message){
					echo '<div class="hb-message notice notice-'.$message['type'].' is-dismissible">
							<p>'.$message['content'].'</p>
							<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
						</div>';
				}
			}else{
				$content = '<center>';
				foreach ($_SESSION['hb']['message'] as $message){
					$content .= '<div class="clearfix"><span class="text-'.$message['type'].'">'.$message['content'].'</span><a href="javascript:void(0)" class=" pull-right" onclick="jQuery(this).parent().remove();"><i class="glyphicon glyphicon-remove-sign"></i></a><div>';
				}
				$content .= '</center>';
				echo '<script>
					var content = \''.$content.'\';
					var div = document.getElementById("main");
					div.innerHTML = content+div.innerHTML;
				</script>';
			}
			
		}
		unset($_SESSION['hb']['message']);
	}
}

//show notify message in backend and front end
function hb_enqueue_message($message,$type='info'){	
	$message = array('content'=>$message,'type'=>$type);
	$_SESSION['hb']['message'][]=$message;
	return true;
}

function hb_register_session(){
	if( !session_id() )
		session_start();
}
add_action('init', 'hb_register_session');
// Add hook for admin <head></head>
function hbpro_add_head_script(){
	echo '<script type="text/javascript">var siteURL="'.site_url().'"; var fvn_url="'.FVN_URL.'"</script>';
}
add_action('admin_head', 'hbpro_add_head_script');
add_action('wp_head', 'hbpro_add_head_script');

function hbpro_plg_scripts() {
	wp_enqueue_script( 'hbpro-plg-js', FVN_URL.'assets/js/hbpro.js', array('jquery'), '1.0.0', true );
	
}


/*
 * Get language code
 */
function hb_getlocale(){
	$lang = get_locale();
	$local = explode('_', $lang);
	return $local[0];
}

//check session expired
add_action('hb_check_cart_expired', 'hb_check_cart_expired');

function hb_check_cart_expired($cart){
	if(!$cart->from){
		wp_safe_redirect(site_url());
		exit;
	}
	return;
}


add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {	
	$user = wp_get_current_user();
	if($user->ID){		
		if (!$user->allcaps['edit_others_posts']) {
			show_admin_bar(false);
			if(is_admin()){
				wp_safe_redirect(site_url());
				exit;
			}
		}
	}
}


if($config->facebook_comment){
	add_action( 'wp_head', 'hb_facebook_meta_tag', 10 );

	function hb_facebook_meta_tag(){
		global $post;
		if ( is_single() ){
			if($post->post_type == 'post') {
				echo '<meta property="og:url" content="'.'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}".'" />';
				echo '<meta property="og:title" content="'.$post->post_title.'" />';
				echo '<meta property="og:description" content="'.$post->post_title.'" />';
				echo '<meta property="og:type" content="website" />';
				echo '<meta property="og:image" content="'.get_the_post_thumbnail_url($post,'large').'" />';
				$config = HBFactory::getConfig();
				$app_id= (string)$config->facebook_app_id;
				echo '<meta property="fb:app_id" content="'.$app_id.'" />';
				echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">';
			}
		}

	}
}


//Dem so luot xem bai viet
function hb_get_post_view($postID){ // hàm này dùng để lấy số người đã xem qua bài viết
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){ // Nếu như lượt xem không có
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0"; // giá trị trả về bằng 0
    }
    return $count; // Trả về giá trị lượt xem
}
function hb_set_post_view($postID) {// hàm này dùng để set và update số lượt người xem bài viết.
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++; // cộng đồn view
        update_post_meta($postID, $count_key, $count); // update count
    }
}

function add_linebreak_shortcode() {
return '<br />';
}
add_shortcode('br', 'add_linebreak_shortcode' );
function clear_br($content) { 
return str_replace("<br/>","<br clear='none'/>", $content);
} 
add_filter('the_content','clear_br');

//login redirect
add_action('init','fvn_custom_login_redirect');

function fvn_custom_login_redirect(){
 global $pagenow;
 if( 'wp-login.php' == $pagenow) {
//   wp_redirect(site_url('login-page'));
//   exit();
 }
}