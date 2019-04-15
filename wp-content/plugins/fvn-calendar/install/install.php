<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
HBImporter::helper('file');
$this_version = '1.0.0';

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
$installed_ver = get_option( 'hb_db_version' );
if(!$installed_ver){
	add_option( 'hb_db_version', $this_version );
}

if( $installed_ver < $this_version ) {
	$path = FVN_PATH.'install/sql';
	$update_files = FvnFileHelper::getFiles($path,'php');
	foreach($update_files as $file){
		if(substr($file,0,-4) > $installed_ver){
			include $path.'/'.$file;
		}
	}
	update_option( 'hb_db_version', $this_version );
}



