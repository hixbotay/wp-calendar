<?php
HBImporter::model('transaction');
HBImporter::helper('math','invest','currency','date');
FvnHelper::checkLogin();

$model = new FvnModelTransaction();
$user = HBFactory::getUser();
$model->setState('filter_user_id',$user->id);
$items = $model->getItems();

add_filter('pre_get_document_title',function(){return 'Các gói đầu tư';});
get_header();
?>
<div class="container">
	<?php echo FvnHelper::renderLayout('user-sidebar',array('layout'=>'mytransaction'))?>
	<h2>Lịch sử giao dịch</h2>
	<table class="table table-bordered">
		<tr>
			<td>Số tiền</td>
			<td>Nội dung</td>
			<td>Ngày cập nhật</td>
		</tr>
	<?php foreach($items as $item){?>
		<tr>
			<td><?php echo FvnCurrencyHelper::displayPrice(FvnInvestHelper::caculateDrawAble($item)['total'])?></td>
			<td><?php echo $item->content?></td>
			<td><?php echo HBDateHelper::display($item->start);?></td>
		</tr>
	<?php }?>
	</table>
</div>
<?php get_footer() ?>