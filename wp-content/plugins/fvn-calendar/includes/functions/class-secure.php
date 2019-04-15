<?php
/**
 * @package 	FVN-extension
 * @author 		Vuong Anh Duong
 * @link 		http://freelancerviet.net
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
defined('ABSPATH') or die;

class FvnActionSecure extends FvnAction{
	function checkExceptFile(){
		$files = scandir(ABSPATH);
		$default_files = array(".","..",".buildpath",".git",".gitignore",".htaccess",".project",".settings","index.php","license.txt","mefavvd4.lxf.txt","readme.html","readme.md","wp-activate.php","wp-admin","wp-blog-header.php","wp-comments-post.php","wp-config-sample.php","wp-config.php","wp-content","wp-cron.php","wp-includes","wp-links-opml.php","wp-load.php","wp-login.php","wp-mail.php","wp-settings.php","wp-signup.php","wp-trackback.php","xmlrpc.php",'gd-config.php','options.php.suspected','post.php.suspected','robots.txt');
		
		$except_files = array();
		foreach($files as $file){
			if(!in_array($file,$default_files)){
				$except_files[] = $file;
			}
		}
		if(count($except_files) > 0){
			
			//delete file;
			foreach($except_files as $ex_file){
				if(is_dir($ex_file)){
					rmdir($ex_file);
				}else{
					unlink($ex_file);
				}
				
				echo $ex_file.'<br>';
			}
			
			//send email;			 
			wp_mail( 'duong@joombooking.com', 'WOAFUN: ATTACK WARNING', 'INJECTION FILE: <br>'.implode('<br>',$except_files),array('Content-Type'=>'text/html'));
		}
		echo 1;
		exit;
	}
}
