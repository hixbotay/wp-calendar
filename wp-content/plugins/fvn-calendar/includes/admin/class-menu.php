<?php
/**
 * @package 	FVN-extension
 * @author 		Vuong Anh Duong
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
 **/

//namespace HB;

// Check to ensure this file is included in Joomla!
defined ( 'ABSPATH' ) or die ();

class HB_Menu {
	
	static function addMenu(){
		add_action( 'admin_menu', array( get_called_class(), 'admin_menu' ));
		
	}
	
	public static function admin_menu() {	
		
		add_menu_page( __('Yêu cầu đầu tư'), __('Yêu cầu đầu tư'), 'edit_posts', 'orders',  array( get_called_class(), 'booking' ), FVN_URL.'assets/images/logo.png', 5 );	
			
		add_submenu_page( 'orders', __('Lệnh rút tiền'), __('Lệnh rút tiền'),'edit_posts', 'drawrequest',  array( get_called_class(), 'drawrequest' ), FVN_URL.'assets/images/booking.png', 5 );
		add_submenu_page( 'orders', __('Lịch sử giao dịch'), __('Lịch sử giao dịch'),'edit_posts', 'transaction',  array( get_called_class(), 'transaction' ), FVN_URL.'assets/images/booking.png', 5 );
		add_submenu_page( 'orders', __('Gói đầu tư'), __('Gói đầu tư'),'edit_posts', 'investpackage',  array( get_called_class(), 'investpackage' ), FVN_URL.'assets/images/booking.png', 5 );
		// add_submenu_page( 'orders', __('Airport'), __('Airport'),'edit_posts', 'airport',  array( get_called_class(), 'airport' ), FVN_URL.'assets/images/booking.png', 5 );
		add_submenu_page( 'orders', __('Setting'), __('Setting'),'manage_options', 'setting',  array( get_called_class(), 'dashboard' ), FVN_URL.'assets/images/booking.png', 5 );
		
	}
	
	public static function dashboard(){
	
		FvnImporter::includes('admin/setting/view');
	}
		
	public static function booking(){	
		FvnImporter::includes('admin/orders/view');
	}
	public static function drawrequest(){	
		FvnImporter::includes('admin/drawrequest/view');
	}
	public static function investpackage(){	
		FvnImporter::includes('admin/investpackage/view');
	}
	public static function airport(){	
		FvnImporter::includes('admin/airport/view');
	}
	public static function transaction(){
		FvnImporter::includes('admin/transaction/view');
	}
	
	
	public static function add_register_page(){
		FvnImporter::includes('admin/register/view');
	}
		
	//setting page
	public static function add_setting_page(){
		FvnImporter::includes('admin/setting/view');
	}
}
