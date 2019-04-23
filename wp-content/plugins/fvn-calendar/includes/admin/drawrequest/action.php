<?php
class FvnActionDrawRequest extends FvnAction{	
    
    public function approve(){        
        $this->setRedirect(admin_url('admin.php?page=drawrequest'));
        $request = $this->getModel();
        $request->load($this->input->get('id'));
        if(!$request->order_id){
            return;
        }
        if($request->status=='APPROVED'){
            hb_enqueue_message(_('Chấp nhận thành công'));
            return;
        }
        
        FvnImporter::model('orders');
        $order = new FvnModelOrders();
        $order->load($request->order_id);
        if($order->order_status != 'CONFIRMED'){
            hb_enqueue_message(_('Gói đầu tư chưa được xác nhận'),'error');
            return;
        }
        if($order->pay_status != 'SUCCESS'){
            hb_enqueue_message(_('Gói đầu tư chưa thanh toán'),'error');
            return;
        }
        try{

            $order->order_status=FvnParamOrderStatus::CLOSED['value'];
            $order->store();

            $request->status = 'APPROVED';
            $request->store();

            $transaction = new FvnModel('#__fvn_transaction','id');
            $transaction->save(array(
                'user_id' => $request->user_id,
                'order_id' => $request->order_id,
                'total' => -$order->caculateDrawAble()['total'],
                'content' => 'Rút tiền từ gói đầu tư '.$order->order_number,
                'created' => current_time( 'mysql' )
            ));

            $mail = new FvnMailHelper($order->id);
            $mail->sendApproveDrawRequest();
            
            hb_enqueue_message(_('Chấp nhận thành công'));
        }catch(Exception $e){
            FvnHelper::write_log('error.txt',$e->getMessage());
            hb_enqueue_message('Save failed','error');
        }

    }

    public function reject(){        
        $this->setRedirect(admin_url('admin.php?page=drawrequest'));
        $request = $this->getModel();
        $request->load($this->input->get('id'));
        if(!$request->order_id){
            return;
        }
        if($request->status=='APPROVED'){
            hb_enqueue_message(_('Giao dịch đã được xác nhận không thể hủy','error'));
            return;
        }
        $request->status = 'REJECTED';
        if(!$request->store()){
            hb_enqueue_message('Save failed','error');
            return;
        }else{
            FvnImporter::helper('email');
            $mail = new FvnMailHelper($request->order_id);
            $mail->sendRejectDrawRequest();
        }
        return;

    }
	
}