<?php
/**
 * @package 	FVN-extension
 * @author 		Joombooking
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined('ABSPATH') or die('Restricted access');

FvnImporter::helper('calendar');
class FvnActionOrder extends FvnAction{

	private function validateOrder($data){]
		if(!$data['start'] || !$data['start_time'] || !$data['end_time']){
			$this->error = 'Vui lòng điền thời gian đặt lịch hẹn';
			return false;
		}
		if($data['start_time'] >= !$data['end_time']){
			$this->error = 'Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc';
			return false;
		}
		if(!$data['type_desc']){
			$this->error = 'Vui lòng điền địa chỉ '.FvnParamVideoCallType::getDisplay($data['type']);
			return false;
		}
		$booked = FvnCalendarHelper::getBooked();
		if(isset($booked[$data['date']])){
			foreach($booked[$data['date']] as $period){
				if(($data['start_time'] > $period['start_time'] && $data['end_time'] <= $period['end_time']) ||
				($data['start_time'] == $period['start_time'] && $data['end_time'] <= $period['end_time']) ||
				($data['start_time'] < $period['start_time'] && $data['end_time'] >= $period['end_time'])){
					$this->error = "<span style='color:red'>Thời gian bạn muốn đặt lịch hẹn đã bị trùng trong ngày: {$data['date']}</span><br><b>Thời gian bạn có thể đặt lịch:</b><br>";
					$availables = FvnCalendarHelper::getAvailable($data['date']);
					foreach($availables as $p){
						$this->error .= "Từ {$p['start_time']} đến {$p['end_time']}<br>";
					}
					return false;
				}
			}
		}
		return true;
	}

	
	public function book(){
		$result = array(
				'status' => 0,
				'error' => array(
						'code' => '',
						'msg' => 'Error'
				)
		);
		//check nonce
		// 		if ( empty( $_REQUEST['hb_meta_nonce'] ) || ! wp_verify_nonce( $_REQUEST['hb_meta_nonce'], 'hb_meta_nonce' ) ) {
		// 			$result['error']['msg'] = __('Session expired');
		// 			return $this->ajax_process_order($result);
		// 		}
		//validate image
		
		FvnImporter::model('orders');
		FvnImporter::helper('math','date','currency');
		
		$data = $this->input->get('jform',array());
		global $wpdb;
		$user = HBFactory::getUser();
		try{
			$order = new FvnModelOrders();
			if(!$user->id){
				throw new Exception('Vui lòng đăng nhập');
			}
			if($data['order_id']){
				$result['order_id'] = $data['order_id'];
				$result['status'] = 1;
				$result['url'] = site_url('/?hbaction=payment&task=process&order_id='.$data['order_id'].'&pay_method='.$this->input->get('pay_method'));
			}else{
				$config = HBFactory::getConfig();			
				
				$data['start'] = FvnDateHelper::createFromFormatYmd($data['start']);	
				$data['user_id'] = $user->id;
				$data['pay_status']= FvnParamPayStatus::PENDING['value'];
				$data['order_status']= FvnParamOrderStatus::PENDING['value'];
				$data['currency'] = $config->main_currency;

				if(!$this->validateOrder($data)){
					throw new Exception($this->error_msg);
				}

				$check = $order->save($data);		
				
				if($check){
					$result['status'] = 1;
					$result['order_id'] = $order->id;
					$result['url'] = site_url('/?hbaction=payment&task=process&order_id='.$order->id.'&pay_method='.$this->input->get('pay_method','offline'));
					// $result['url'] = FvnHelper::get_order_link($order);
				}else{
					$result['error']['msg'] = $wpdb->last_error;					
				}
			}

		}catch (Exception $e){
			$result['error']['msg'] = $e->getMessage();
		}
		echo $this->renderJson($result);
		exit;
	}
	
	
}