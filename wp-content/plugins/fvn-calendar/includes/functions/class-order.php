<?php
/**
 * @package 	FVN-extension
 * @author 		Joombooking
 * @link 		http://http://woafun.com/
 * @copyright 	Copyright (C) 2011 - 2012 Vuong Anh Duong
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined('ABSPATH') or die('Restricted access');

class FvnActionOrder extends FvnAction{
	private function validateOrder($data){
		if($data['day']<1){
			return false;
		}
		$package= (new FvnModelInvestPackage())->getItem($data['invest_package_id']);
		if($package->type==FvnParamInvestType::MONTHLY['value']){
			if($data['day']<30) return false;
		}
		if($package->type==FvnParamInvestType::YEAR['value']){
			if($data['day']<365) return false;
		}
		return true;
	}

	public function ajax_caculate_revenue(){
		HBImporter::helper('invest','currency','date');
		$data = $this->input->getPost();
		$result = FvnInvestHelper::caculateDrawAble(array(
			'total'=>$data['total'],
			'start' => date('Y-m-d'),
			'end' => HBDateHelper::getDate()->modify('+'.$data['day'].' days')->format('Y-m-d'),
			'invest_package_id'=>$data['invest_package_id']
		),true);
		$result['total'] = FvnCurrencyHelper::displayPrice($result['total']);
		$result['revenue'] = FvnCurrencyHelper::displayPrice($result['revenue']);
		$this->renderJson($result);
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
		
		HBImporter::model('investpackage','orders');
		HBImporter::helper('math','date','currency');
		
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
				
				$data['start'] = HBFactory::getDate()->format('Y-m-d');			
				$data['total'] = $data['total'];
				$data['notes'] = $data['notes'];
				$data['user_id'] = $user->id;
				$data['pay_status']="PENDING";
				$data['order_status']="PENDING";
				$data['invest_package_id'] = $data['invest_package_id'];
				$data['type'] = 'INVEST';
				$data['currency'] = $config->main_currency;
				$data['start'] = HBDateHelper::getDate()->format('Y-m-d');
				$data['end'] = HBDateHelper::getDate()->modify("+{$data['day']} days")->format('Y-m-d');

				if(!$this->validateOrder($data)){
					throw new Exception('Dữ liệu không hợp lệ');
				}

				$check = $order->save($data);		
				
				if($check){
					$result['status'] = 1;
					$result['order_id'] = $order->id;
					$result['url'] = site_url('/?hbaction=payment&task=process&order_id='.$order->id.'&pay_method='.$this->input->get('pay_method'));
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
	
	public function draw(){
		HBImporter::model('drawrequest','orders');
		HBImporter::helper('email');
		$order = new FvnModelOrders();
		$order->load($this->input->getInt('order_id'));
		FvnHelper::checkLogin();

		if(!$order->id){
			wp_die('Invalid order');
		}
		if($order->order_status!="CONFIRMED" && $order->pay_status!='SUCCESS'){
			hb_enqueue_message('Gói đầu tư chưa được thanh toán hoặc chưa được xác nhận không đủ điều kiện rút tiền');
			wp_safe_redirect(site_url('/myorders'));
			exit;
		}
		//kiem tra xem order da dc yeu cau rut tien trc do chua
		$allow_draw = true;
		$draw_request = (new FvnModelDrawRequest())->getRequestByOrder($order->id);
		foreach($draw_request as $req){
			if($req->status=='PENDING' || $req->status=='APPROVED'){
				$allow_draw=false;
			}
		}
		if($allow_draw){
			$draw = new FvnModelDrawRequest();
			$check = $draw->save(array(
				'invest_package_id' => $order->invest_package_id,
				'order_id' => $order->id,
				'user_id'=>$order->user_id,
				'status' =>'PENDING',
				'created'=>current_time( 'mysql' )
			));
			if($check){
				//gui mail cho admin
				$mail = new FvnMailHelper($order->id);
				$mail->sendDrawRequestAdmin();

				hb_enqueue_message('Đăng kí rút tiền thành công đang chờ Admin duyệt');
			}
		}
		
		
		wp_safe_redirect(site_url('/mydrawrequest/'));
		exit;
	}
}