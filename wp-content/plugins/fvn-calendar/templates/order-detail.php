<?php 
FvnImporter::model('orders');
FvnImporter::helper('currency', 'date');

$input = HBFactory::getInput();
$order = (new FvnModelOrders())->getComplexItem($input->getInt('order_id'));

// debug($order);
add_filter('pre_get_document_title', function () {
	return 'Chi tiết lịch hẹn ' . $package->name;
});
?>
<div class="row">
    <div class="col-sm-3">Họ và tên</div>
    <div class="col-sm-9"><?php echo $order->order->name?></div>
</div>
<div class="row">
    <div class="col-sm-3">Điện thoại liên hệ</div>
    <div class="col-sm-9"><?php echo $order->order->phone?></div>
</div>
<div class="row">
    <div class="col-sm-3">Ngày gọi</div>
    <div class="col-sm-9"><?php echo $order->order->start?></div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="col-sm-6">Thời gian bắt đầu</div>
            <div class="col-sm-6"><?php echo $order->order->start_time?></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-sm-6">Thời gian kết thúc</div>
            <div class="col-sm-6"><?php echo $order->order->end_time?></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">Liên hệ qua</div>
    <div class="col-sm-9">
        <?php echo FvnParamVideoCallType::getDisplay($order->order->type).' '.$order->order->type_desc?>
    </div>
</div>

<div class="row">
    <div class="col-sm-3">Chú ý</div>
    <div class="col-sm-9"><?php echo $order->order->notes?></div>
</div>

<div class="row">
    <div class="col-sm-3">Trạng thái</div>
    <div class="col-sm-9"><?php echo FvnParamOrderStatus::getDisplay($order->order->order_status)?></div>
</div>