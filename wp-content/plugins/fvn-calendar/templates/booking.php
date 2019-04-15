<?php
HBImporter::model('orders');
HBImporter::helper('math','date','currency');
// FvnHelper::checkLogin();

$input = HBFactory::getInput();
$order_id = $input->getInt('order_id');
$model = new FvnModelOrders();
$user = HBFactory::getUser();
$order_complex = $model->getComplexItem($order_id);
$item = $order_complex->order;

add_filter('pre_get_document_title',function(){return 'Đặt lịch hẹn';});
get_header();
?>
<div class="container">
	<?php echo FvnHelper::renderLayout('user-sidebar',array('layout'=>'orders'))?>

	<h2>Chi tiết gói đầu tư</h2>
	<table class="table table-bordered">
		<tr>
			<td>Mã giao dịch</td>
			<td>Gói đầu tư</td>
			<td>Tổng tiền đầu tư</td>
			<td>Trạng thái</td>
			<td>Dự kiến</td>
			<td>Ngày bắt đầu</td>
		</tr>
		<tr>
			<td><?php echo $item->order_number?></td>
			<td><?php echo $order_complex->package->name?></td>
			<td><?php echo FvnCurrencyHelper::displayPrice($item->total)?></td>
			<td><?php echo FvnParamOrderStatus::getDisplay($item->order_status)?></td>
			<td><?php echo FvnCurrencyHelper::displayPrice(FvnInvestHelper::caculateDrawAble($item)['total'])?></td>
			<td><?php echo HBDateHelper::display($item->start);?></td>
		</tr>
	</table>
	<?php if($allow_draw){?><a href="<?php echo site_url('?hbaction=order&task=draw&order_id='.$order_id)?>" class="btn btn-primary">Rút tiền</a><?php }?>
	<a href="<?php echo site_url('/myorders')?>" class="btn btn-primary">Quay lại</a>
</div>
<?php get_footer() ?>