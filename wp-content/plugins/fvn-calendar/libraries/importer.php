<?php

/**
 * Support for generating html code
 *
 * @package 	jbtransport
 * @author 		Vuong Anh Duong
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/

//namespace HB;

defined('ABSPATH') or die('Restricted access');
class FvnImporter
{
	static $libs = array();
	/**
	 * Add bootstrap library
	 */
	static function addBootstrap(){
		wp_enqueue_script( 'bootstrap-js',FVN_URL.'assets/js/bootstrap.min.js');
		wp_enqueue_style( 'bootstrap-css', FVN_URL.'assets/css/bootstrap.min.css');
	}
    /**
     * Import file by absolute path from component root. 
     *
     * @param string $base last directory contain files: helpers, models, elements ...
     * @param array $names files names without path and extensions
     * @param string extension without dot: php, html, ini ...
     */
    static function import($base, $names, $ext = 'php')
    {
        if (! is_array($names)) {
            $names = array($names);
        }
        
        $filePathMask = FVN_PATH  . $base . '/%s.' . $ext;
        foreach ($names as $name) {
            self::importFile(sprintf($filePathMask, $name));
        }
    }
    static function classes(){
    	$names = func_get_args();
    	self::import('includes/classes', $names);
    }
	
	static function apps(){
    	$names = func_get_args();
    	self::import('apps', $names);
    }
	

    static function importFile($filePath, $error = true)
    { 
    	include_once ($filePath);
    }

    /**
     * Link js or css file into html head.
     *
     * @param string $type source type, available values are 'js' or 'css'
     * @param array $names files names without extension and path
     */
    static function link($type, $names)
    {
        $absMask = FVN_PATH . 'assets' . DS . $type . DS . '%s.' . $type;
        $langMask = FVN_PATH . DS . 'assets' . DS . 'language' . DS . '%s.php';
        $realMask = get_home_url() . '/wp-content/plugins/woafun/assets/' . $type . '/%s.' . $type;
        foreach ($names as $name) {
            $abs = sprintf($absMask, $name);
            $real = sprintf($realMask, $name);
            
            if (file_exists($abs)) {
                switch ($type) {
                    case 'js':
                       wp_enqueue_script( 'HB_'.$name, $real);                      
                        break;
                    case 'css':
                    	wp_enqueue_style( 'HB_'.$name, $real);
                }
            } else {
                HB_enqueue_message('File ' . $name . ' not found','error');
            }
        }
    }

    /**
     * Import component controller. Current or default according to client position.
     * @return boolean true if successfully imported
     */
 	static  function viewaction($name = null)
    {
 		$names = func_get_args();
    	foreach($names as $name){
    		require_once FVN_PATH.'includes/admin/'.$name.'/action.php';
    	}
    }
    
	static  function includes($name = null)
    {
       $names = func_get_args();
        self::import('includes', $names);
    }
    
    static  function libraries($name = null)
    {
    	$names = func_get_args();
    	foreach ($names as $name){
    		//avoid include a class twice with diffrent location
    		if(!isset(self::$libs[$name])){
    			self::import('libraries', $name);
    			self::$libs[$name] = 1;
    		}
    	}
    	return true;
    }
    
    
    
    //import function file
    static  function functions($name = null)
    {
    	$names = func_get_args();
    	foreach($names as $name){		
    		require_once FVN_PATH.'includes/functions/class-'.$name.'.php';
    	}
    }
    
    static  function metabox($name = null)
    {
    	$names = func_get_args();
    	foreach($names as $name){
    		require_once FVN_PATH.'includes/admin/'.$name.'/metabox.php';
    	}
    }
   
    

    /**
     * Import component helper.
     *
     * @param mixed $name file name without extension and path
     */
    static function helper($name)
    {
        $names = func_get_args();
        self::import('includes/helpers', $names);
    }

    static function params($name)
    {
        $names = func_get_args();
        self::import('includes/glossary', $names);
    }

    /**
     * Import component model.
     *
     * @param mixed $name file name without extension and path.
     */
    static function model($name)
    {
        $names = func_get_args();
        foreach($names as $name){
        	self::import('includes/admin/'.$name, 'model');
        }        
    }

    /**
     * Import component table.
     *
     * @param mixed $name file name without extension and path.
     */
   static function table($name)
    {
        $names = func_get_args();
        self::import('tables', $names);
    }

    /**
     * Link js source into html head.
     *
     * @param mixed $name file name without extension and path
     */
    static function js($name)
    {
        $names = func_get_args();
        self::link('js', $names);
    }

    /**
     * Link css source into html head.
     *
     * @param mixed $name file name without extension and path
     */
   static  function css($name)
    {
        $names = func_get_args();
        self::link('css', $names);
    }
	
	

    
    static public function find($path, $file)
	{
		// Start looping through the path set
		// Get the path to the file
		$fullname = $path . '/' . $file.'.php';
		// Is the path based on a stream?
		if (strpos($path, '://') === false)
		{
			// Not a stream, so do a realpath() to avoid directory
			// traversal attempts on the local file system.

			// Needed for substr() later
			$path = realpath($path);
			$fullname = realpath($fullname);
		}

		/*
		 * The substr() check added to make sure that the realpath()
		 * results in a directory registered so that
		 * non-registered directories are not accessible via directory
		 * traversal attempts.
		 */
		if (file_exists($fullname) && substr($fullname, 0, strlen($path)) == $path)
		{
			return $fullname;
		}

		// Could not find the file in the set of paths
		return false;
	}
	
	static function corePaymentPlugin(){
		$files = scandir (FVN_PATH.'includes/gateways');
		foreach ($files as $plugin){
			if(preg_match('/^hbpayment_\w*/', $plugin)){
				require_once FVN_PATH."includes/gateways/{$plugin}/{$plugin}.php";
			}
		}
	}

}

?>