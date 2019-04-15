<?php
/**
 * @package 	FVN-extension
 * @author 		Vuong Anh Duong
 * @link 		http://freelancerviet.net
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
require_once 'model/query.php';
require_once 'model/state.php';
require_once 'model/pagination.php';

class FvnUser
{
	public $id;
	public $email;
	public $meta;

	function __construct($id)
	{
		$this->id = $id;
		$user = get_user_by('ID',$id);
		if($user->ID){
			foreach($user as $key=>$val){
				$this->$key = $val;
			}
			foreach($user->data as $key=>$val){
				$this->$key = $val;
			}
			$meta = get_user_meta($user->ID);
			foreach($meta as $key=>$val){
				$this->meta[$key] = reset($val);
				$this->$key = reset($val);
			}
			$this->email = $user->user_email;
		}
	}
	
}