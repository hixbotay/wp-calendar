<?php
FvnImporter::model('drawrequest');
FvnImporter::helper('math','invest','currency','date');
FvnHelper::checkLogin();

$model = new FvnModelDrawRequest();
$user = HBFactory::getUser();
$model->setState('filter_user_id',$user->id);
$items = $model->getItems();

add_filter('pre_get_document_title',function(){return 'Trạng thái rút tiền';});
get_header();
?>
<div class="container">
	<?php echo FvnHelper::renderLayout('user-sidebar',array('layout'=>'mydrawrequest'))?>
	<h2>Trạng thái lệnh rút tiền</h2>
	<div class="well well-small">
		<a class="btn btn-primary" href="<?php echo site_url('/myorders')?>">Tạo lệnh rút tiền mới</a>
	</div>
	<table class="table table-bordered">
		<tr>
			<td>Mã giao dịch</td>
			<td>Gói đầu tư</td>
			<td>Trạng thái</td>
			<td>Dự kiến</td>
			<td>Ngày tạo</td>
		</tr>
	<?php foreach($items as $item){?>
		<tr>
			<td><?php echo $item->order_number?></td>
			<td><?php echo $item->invest_name?></td>
			<td><?php echo $item->status?></td>
			<td><?php echo FvnCurrencyHelper::displayPrice(FvnInvestHelper::caculateDrawAble($item)['total'])?></td>
			<td><?php echo FvnDateHelper::display($item->created);?></td>
		</tr>
	<?php }?>
	</table>
</div>
<?php get_footer() ?>