<?php
FvnImporter::model('orders');
FvnImporter::helper('math','invest','currency','date');
FvnHelper::checkLogin();

$model = new FvnModelOrders();
$user = HBFactory::getUser();
$items = $model->getOrderByUser($user->id,['success'=>1]);

add_filter('pre_get_document_title',function(){return 'Các gói đầu tư';});
get_header();
?>
<div class="container">
	<?php echo FvnHelper::renderLayout('user-sidebar',array('layout'=>'myorders'))?>
	<h2>Các gói đầu tư</h2>
	<div class="well well-small">
		<a class="btn btn-primary" href="<?php echo site_url()?>/#goi-dau-tu">Thêm gói đầu tư</a>
	</div>
	<table class="table table-bordered">
		<tr>
			<td>Mã giao dịch</td>
			<td>Gói đầu tư</td>
			<td>Tổng tiền đầu tư</td>
			<td>Trạng thái</td>
			<td>Dự kiến</td>
			<td>Ngày bắt đầu</td>
		</tr>
	<?php foreach($items as $item){?>
		<tr>
			<td><a href="<?php echo site_url('/orderdetail/?order_id='.$item->id)?>"><?php echo $item->order_number?></a></td>
			<td><?php echo $item->invest_name?></td>
			<td><?php echo $item->total?></td>
			<td><?php echo FvnHelper::getOrderStatus($item)?></td>
			<td><?php echo FvnCurrencyHelper::displayPrice(FvnInvestHelper::caculateDrawAble($item)['total'])?></td>
			<td><?php echo FvnDateHelper::display($item->start);?></td>
		</tr>
	<?php }?>
	</table>
</div>
<?php get_footer() ?>