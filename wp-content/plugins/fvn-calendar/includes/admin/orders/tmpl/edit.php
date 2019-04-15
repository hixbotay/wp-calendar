<?php 
	
HBImporter::model('orders');
HBImporter::helper('math','date','currency','orderstatus','paystatus','invest');
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
				<div class="col-md-7"><?php echo $order_complex->user->display_name?></div>
			</div>						
			<div class="row form-group">
                <div class="col-md-5">Điện thoại</div>
				<div class="col-md-7"><?php echo $order_complex->user->phone?></div>
			</div>
			
			<div class="row form-group">
                <div class="col-md-5">Email</div>
				<div class="col-md-7"><?php echo $order_complex->user->email?></div>
			</div>
			
			<div class="row form-group">
                <div class="col-md-5">Notes</div>
				<div class="col-md-7"><textarea type="text" name="data[notes]" class="regular-text ltr"><?php echo $this->item->notes ?></textarea></div>
			</div>
			<div class="row form-group">
                <div class="col-md-5">Tổng tiền</div>
				<div class="col-md-7"><?php echo FvnCurrencyHelper::displayPrice($order_complex->order->total)?></div>
			</div>
			<div class="row form-group">
                <div class="col-md-5">Gói đầu tư</div>
				<div class="col-md-7"><?php echo $order_complex->package->name?></div>
			</div>
			<div class="row form-group">
                <div class="col-md-5">Lãi xuất thêm</div>
				<div class="col-md-7">+<?php echo FvnCurrencyHelper::displayPrice(FvnInvestHelper::caculateDrawAble($this->item)['revenue'])?></div>
			</div>
			


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
		<div class="row form-group">
			<div class="col-md-5"><?php echo __('Transaction id')?></div>
			<div class="col-md-7"><?php echo $this->item->pay_method.' '.($this->item->tx_id)?></div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-5"><?php echo __('Created')?></div>
			<div class="col-md-7"><?php echo HBDateHelper::display($this->item->created,'M d Y H:i')?></div>
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