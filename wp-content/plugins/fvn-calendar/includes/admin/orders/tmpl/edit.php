<?php 
	
FvnImporter::model('orders');
FvnImporter::helper('math','date','currency','orderstatus','paystatus');
FvnHtml::add_datepicker_lib();
$config = HBFactory::getConfig();

$order_complex = (new FvnModelOrders())->getComplexItem($this->item->id);
// debug($this->item->params);die;
// debug($order_complex->user);
?>

<h1>Quản lí đơn <a href="<?php echo admin_url('admin.php?page=orders')?>" class="page-title-action" >Quay lại</a></h1>
<form action="<?php echo admin_url('admin.php?fvnaction=orders&task=save')?>" method="post">
<div class="container">
<div id="primary" class="row">
	<div class="col-md-8">
		
        <input type="hidden" value="<?php echo $this->input->get('id')?>" name="id"/>
			<div class="row form-group">
                <div class="col-md-5"><?php echo __('Họ tên') ?></div>
				<div class="col-md-7"><?php echo $order_complex->order->name?></div>
			</div>						
			<div class="row form-group">
                <div class="col-md-5">Điện thoại</div>
				<div class="col-md-7"><?php echo $order_complex->order->phone?></div>
			</div>
			
			<div class="row form-group">
                <div class="col-md-5">Email</div>
				<div class="col-md-7"><?php echo $order_complex->order->email?></div>
			</div>
			<div class="row form-group">
                <div class="col-md-5">Giới tính</div>
				<div class="col-md-7"><?php echo FvnHtml::select(FvnParamGender::getAll(),'data[gender]','required class=""','value','display',$this->item->gender,'type')?></div>
			</div>
			<div class="row form-group">
                <div class="col-md-5">Notes</div>
				<div class="col-md-7"><textarea type="text" name="data[notes]" class="regular-text ltr"><?php echo $this->item->notes ?></textarea></div>
			</div>
			<div class="row form-group">
                <div class="col-md-5">Ngày</div>
				<div class="col-md-7"><?php echo FvnHtml::calendar($this->item->start,'data[start]','start',FvnDateHelper::getConvertDateFormat(),'')?></div>
			</div>
			<div class="row form-group">
                <div class="col-md-5">Bắt đầu</div>
				<div class="col-md-7"><?php echo FvnHtml::timePicker('data[start_time]','',$this->item->start_time,'start_time')?></div>
			</div>
			<div class="row form-group">
                <div class="col-md-5">Kết thúc</div>
				<div class="col-md-7"><?php echo FvnHtml::timePicker('data[end_time]','',$this->item->end_time,'end_time')?></div>
			</div>
			<!-- <div class="row form-group">
                <div class="col-md-5">Tổng tiền</div>
				<div class="col-md-7"><?php echo FvnCurrencyHelper::displayPrice($order_complex->order->total)?></div>
			</div> -->
						


		<?php wp_nonce_field( 'hb_action', 'hb_meta_nonce' );?>
		<center><button type="submit" class="btn btn-primary btn-lg"><?php echo __('Save')?></button></center>
	
	</div>
	<div class="col-md-4">
		<div class="row form-group">
			<div class="col-md-5"><?php echo __('Order number')?></div>
			<div class="col-md-7"><?php echo $this->item->order_number?></div>
		</div>
		<div class="row form-group">
			<div class="col-md-5"><?php echo __('Order status')?></div>
			<div class="col-md-7"><?php echo OrderStatus::getHtmlList('data[order_status]', '', $this->item->order_status)?></div>
		</div>
		<div class="row form-group">
			<div class="col-md-5"><?php echo __('Pay status')?></div>
			<div class="col-md-7"><?php echo PayStatus::getHtmlList('data[pay_status]', '', $this->item->pay_status)?></div>
		</div>
		<!-- <div class="row form-group">
			<div class="col-md-5"><?php echo __('Transaction id')?></div>
			<div class="col-md-7"><?php echo $this->item->pay_method.' '.($this->item->tx_id)?></div>
		</div> -->
		
		<div class="row form-group">
			<div class="col-md-5"><?php echo __('Created')?></div>
			<div class="col-md-7"><?php echo FvnDateHelper::display($this->item->created,'M d Y H:i')?></div>
		</div>
		
	</div>
	
	
</div><!-- #primary -->
</div>
</form>
<style>
.upload-image-section{
border:1px solid #ccc;
padding:5px;
}</style>