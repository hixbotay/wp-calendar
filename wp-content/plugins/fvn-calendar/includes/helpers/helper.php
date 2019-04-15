<?php

/**
 * @package 	FVN-extension
 * @author 		Vuong Anh Duong
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 80 2012-08-10 09:25:35Z quannv $
 **/

defined('ABSPATH') or die('Restricted access');

class FvnHelper
{
	static function formatArrayForCloneForm($arrays){
		$result = array();
		
		foreach ($arrays as $key=>$array){
			foreach ($array as $i=>$val){
				$result[$i][$key] = $val;
			}
			
		}
		return $result;
	}
	/*Clear value for memory*/
	static function clear(&$value){
		$value = null;
		unset($value);
		return;
	}
	static function isAgent($id = null){
		$checked = false;
		$user = JFactory::getUser($id);
		if(JComponentHelper::getParams('com_bookpro')->get('agent_usergroup') && $user->groups){
			if(in_array(JComponentHelper::getParams('com_bookpro')->get('agent_usergroup'), $user->groups)){
				$checked = true;
				}
		}

		return $checked;
	}
	
	static function getAgentParams($id = null){
			
		$user = JFactory::getUser($id);
	
		if(empty($user)){
			return false;
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from($db->quoteName('#__bookpro_customer'));
		$query->where($db->quoteName('user').' = '.$user->id);
		$db->setQuery($query);
		$data = $db->loadResult();
		$result = json_decode($data);
		return $result;
	}
    
	
	static function renderLayout($name,$displayData=null,$path='layouts'){
		$file = $path.DS.$name.'.php';
		$template = HB_Template_Loader::getRoot($file);
		if(!$template){
			$template = FVN_PATH ."templates/$file";
		}
		
		ob_start();
		include $template;
		$layoutOutput = ob_get_contents();
		ob_end_clean();
		return $layoutOutput;
	}
	
	static function getRangeSelect($selected){

		$option[] = JHtml:: _('select.option',0, JText::_("COM_BOOKPRO_QUICK_FILTER_DATE"));
		$option[] = JHtml:: _('select.option','today', JText::_("COM_BOOKPRO_TODAY"));
		$option[] = JHtml:: _('select.option','past_week', JText::_("COM_BOOKPRO_LAST_WEEK"));
		$option[] = JHtml:: _('select.option', 'past_1month', JText::_("COM_BOOKPRO_LAST_MONTH"));
		return JHtml::_('select.genericlist',$option,'filter_range','onchange="this.form.submit();" class="input input-medium"','value','text',$selected);

	}


	static function formatGender($gender,$symbol=false){
		if($gender=='M') {
			if($symbol)
				return 'M';
			else
				return __('Male');
		}
		else
			if($symbol)
				return 'F';
			else
				return __('Female');
	}

	static function formatAge($age){
		switch ($age){
			case 1 :
				return JText::_('COM_BOOKPRO_ADULT');
				break;
			case 2:
				return JText::_('COM_BOOKPRO_CHILDREN');
				break;
			case 3 :
				return JText::_('COM_BOOKPRO_INFANT');
				break;
		}

	}

	static function getGender(){

		return array(array('value'=>'M','text'=>JText::_('COM_BOOKPRO_MALE')),array('value'=>'F','text'=>JText::_('COM_BOOKPRO_FEMALE')));
	}
	static function getAge(){

		return array(array('value'=>1,'text'=>JText::_('COM_BOOKPRO_ADULT')),array('value'=>0,'text'=>JText::_('COM_BOOKPRO_CHILDREN')),array('value'=>2,'text'=>JText::_('INFANT')));
	}

	
	/**
	 * Get e-mail mode value to using in JUtility::sendMail().
	 *
	 * @param int $emailMode setting value
	 * @return boolean true HTML, false PLAIN TEXT
	 */
	function getEmailMode($emailMode)
	{
		$emailMode = $emailMode != PLAIN_TEXT;
		return $emailMode;
	}

	/**
	 * Convert HTML code to plain text. Paragraphs (tag <p>) and
	 * break line (tag <br/>) replace by end line sign (\n or \r\n)
	 * and remove all others HTML tags.
	 *
	 * @param $string to convert
	 * @return $string converted to plain text
	 */
	function html2text($string)
	{
		$string = str_replace('</p>', '</p>' . PHP_EOL, $string);
		$string = str_replace('<br />', PHP_EOL, $string);
		$string = strip_tags($string);

		return $string;
	}

	static function sendMail($to, $subject, $body , $headers = null, $attachments = null,$from_name=null,$from_email=null,$cc=null)
	{
		if(!$headers){
			$headers = array('Content-Type: text/html; charset=UTF-8');
			//$headers[] = 'From: Me Myself <me@example.net>';
			//$headers[] = 'Cc: John Q Codex <jqc@wordpress.org>';
			//$headers[] = 'Cc: iluvwp@wordpress.org'; // note you can just use a simple email address
		}else{
			$headers[] = 'Content-Type: text/html; charset=UTF-8';			
		}
		if($from_name){
			$headers[] = "From: {$from_name} <{$from_email}>";
		}
		
		return wp_mail(explode(';',$to), $subject, $body, $headers, $attachments );
	}
	
	static function getPassengerForm($adult,$children,$infant){
		$passengers = array();
		$number = 0;
		if ($adult) {
			for($i =0;$i < $adult;$i++){
				$passenger = new stdClass();
				$passenger->title = sprintf(__('Adult %s','hb'),$i+1);
				$passenger->group_id = 1;
				$passenger->type = 'adult';
				$passenger->number = $number;
				$passengers[] = $passenger;
				$number++;
			}
		}
		if ($children) {
			for($i =0;$i < $children;$i++){
				$passenger = new stdClass();
				$passenger->title = sprintf(__('Child %s','hb'),$i+1);
				$passenger->group_id = 2;
				$passenger->type = 'children';
				$passenger->number = $number;
				$passengers[] = $passenger;
				$number++;
			}
		}
		if ($infant) {
			for($i =0;$i < $infant;$i++){
				$passenger = new stdClass();
				$passenger->title = sprintf(__('Infant %s','hb'),$i+1);
				$passenger->group_id = 3;
				$passenger->type = 'infant';
				$passenger->number = $number;
				$passengers[] = $passenger;
				$number++;
			}
		}
		return $passengers;
	}
	
	//ping to url with timeout 1s
	static function pingUrl($url=NULL)  
	{  
	    if($url == NULL) return false;  
	    $ch = curl_init($url);  
	    curl_setopt($ch, CURLOPT_TIMEOUT,1);  
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
	    $data = curl_exec($ch);  
//	    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
	    curl_close($ch);  
	    return $data;
	}

	static function getDataFromApi($url,$method="GET", $data=[],$header=[]){
		if($url == NULL) return false;  
	    $ch = curl_init($url);  
	    // curl_setopt($ch, CURLOPT_TIMEOUT,1);  
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
			if($method=='POST'){
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 1);
				if($header['Content-Type']=='json'){
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));					
				}else{
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));					
				}
			}else{
				if(count($data)){
					curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($data));
				}
			}
			//header
			$curl_header=[];
			foreach($header as $key=>$val){
				$curl_header[] = $key.': '.$val;
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_header);

			
	    $data = curl_exec($ch);
	    curl_close($ch);  
	    return $data;
	}
	
	
	static function write_log($log_file, $error){
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$date = date('d/m/Y H:i:s');
		$error = $date.": ".$error."\n";
		
		$log_file = ABSPATH."logs/".$log_file;
		if(filesize($log_file) > 1048576 || !file_exists($log_file)){
			$fh = fopen($log_file, 'w');
		}
		else{
			//echo "Append log to log file ".$log_file;
			$fh = fopen($log_file, 'a');
		}
		
		fwrite($fh, $error);
		fclose($fh);
	}
	
	public static function check_nonce($name= 'hb_meta_nonce', $action = 'hb_action'){
		$input = HBFactory::getInput();
		$name_str = $input->get($name);
		if ( empty($name_str ) || ! wp_verify_nonce( $input->get($name), $action ) ) {
			wp_die(__('Invalid action!'));
		}
	}
	
	public static function translate_eng($str){
		$str = (string)$str;
		$str = preg_replace("/(à|á|ạ|ả|ã|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
		$str = preg_replace("/(đ)/", "d", $str);
		$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
		$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
		$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
		$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
		$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
		$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
		$str = preg_replace("/(Đ)/", "D", $str);
		return $str;
	}
	
	static function random_string($length){
		$order = '';
		$chars = "0123456789abcdefghijklmnopqrstwvxyzABCDEFGHIJKLMNOPQRSTWVXYZ";
		srand((double)microtime()*1000000);
		$i = 0;
		$total_length = strlen($chars);
		while ($i < $length) {
			$num = rand() % $total_length;
			$tmp = substr($chars, $num, 1);
			$order = $order . $tmp;
			$i++;
		}
		return $order;
	}
	
	static function get_order_link($order){
		// return site_url().'/?view=orderdetail&order_number='.$order->order_number.'&email='.$order->email;
		return site_url().'/orderdetail?order_id='.$order->id;
	}

	static function getPaymentLink($order){
		if(!$order->id){
			return site_url('message');
		}else{
			return site_url().'/payment?order_id='.$order->id;
		}
		
		
	}
	

	static function get_payment_method($status){
		$text = null;

		switch ($status){
			case 'cash':
                $text = "Tiền mặt";
                break;
			case 'onepay':
				$text = "Onepay";
				break;
        }
        return $text;
	}
	
	static function get_url_path(){
		$path_info = substr($_SERVER['PHP_SELF'], 0, -9);
		$url = substr($_SERVER ['REQUEST_URI'],strlen($path_info));
		$url = explode('/', $url);

		foreach($url as &$path){
			$end = strpos($path, "?");
			if($end){
				$path = substr($path, 0,$end);
			}
		}
		return $url;
	}
	
	static function clean_string($string) {
		$string = self::translate_eng($string);
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		return preg_replace('/[^A-Za-z0-9\-]/', '-', $string); // Removes special chars.
	}

	static function checkLogin(){
		$user = HBFactory::getUser();
		if(!$user->id){
			wp_redirect(site_url('/login-page'));
			exit;
		}
		return;
	}

	static function getOrderStatus($order){
		return $order->order_status;
	}

}




?>
