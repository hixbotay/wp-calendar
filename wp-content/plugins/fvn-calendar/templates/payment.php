<?php 
FvnImporter::model('investpackage','orders');
FvnImporter::helper('currency', 'date','invest');
FvnHelper::checkLogin();

$input = HBFactory::getInput();
if($input->getInt('order_id')){
	$order = (new FvnModelOrders())->getComplexItem($input->getInt('order_id'));
    $total = $order->order->total;
}else{
    $total = $input->getInt('total');
}
// debug($order);
add_filter('pre_get_document_title', function () {
	return 'Thanh toán Gói đầu tư ' . $package->name;
});
// wp_enqueue_style( 'visa', FVN_URL.'assets/css/visa.css', '', '1.0.0' );
$user = HBFactory::getUser();

if($total<$package->min_price || $day<1){
	wp_die('404 NOT FOUND');
}
$payment_plugin = HBList::getPaymentAvailPlugin();
$default_plugin = $input->get('payment_method',reset($payment_plugin)->name);
// debug($payment_plugin);die;
?>
<?php get_header(); ?>
<div class="container">
    <form id="frontForm" name="frontForm" action="<?php echo site_url()?>" method="POST">
        <h3>Vui lòng xem lại thông tin về gói đầu tư</h3>

        <div class="content clearfix"><?php echo $package->description ?></div>
        <hr>
        <div class="row">
            <div class="col-sm-3">Lãi xuất</div>
            <div class="col-sm-9" id="lai_xuat">
                <?php if ($package->type == FvnParamVideoCallType::MONTHLY['value']) { ?>
                <?php foreach ($package->rate as $month => $rate) {
									$month = $month < 12 ? "{$month} tháng" : ($month == 12 ? "1 năm" : ">12 tháng");
									echo "{$month}: {$rate}%<br>";
								} ?>
                <?php 
							} else {
								echo reset($package->rate) . '%';
							} ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">Số tiền đầu tư</div>
            <div class="col-sm-9"><?php echo FvnCurrencyHelper::displayPrice($total) ?></div>
        </div>
        <div class="row">
            <div class="col-sm-3">Số ngày</div>
            <div class="col-sm-9"><?php echo $day ?> ngày</div>
        </div>
        <div class="row">
            <div class="col-sm-3">Số tiền lãi dự tính</div>
            <div class="col-sm-9"><?php echo FvnCurrencyHelper::displayPrice($result['revenue']).' + '.FvnCurrencyHelper::displayPrice($total).' = '.FvnCurrencyHelper::displayPrice($result['total'])?></div>
        </div>
        <div class="row">
            <div class="col-sm-3">Chú ý</div>
            <div class="col-sm-9"><textarea class="form-control" rows="15" name="data[notes]"></textarea></div>
        </div>

        <br>
        <h3><?php echo __('Chọn phương thức thanh toán') ?></h3>

        <div id="payment-area" style="display:none">
							<?php foreach($payment_plugin as $p){?>
								<p>
								<input type="radio" id="payment_method_<?php echo $p->name?>" name="pay_method" value="<?php echo $p->name?>"/> <?php echo $p->params->display_name?>          
							</p>
							<?php }?>
        </div>
				<pre id="payment_description"></pre>
        <input type="hidden" name="jform[order_id]" value="<?php echo $input->getInt('order_id') ?>" />
        <input type="hidden" name="hbaction" value="order" />
        <input type="hidden" name="task" value="book" />
				<input type="hidden" name="jform[invest_package_id]" value="<?php echo $id?>" />
				<input type="hidden" name="jform[total]" value="<?php echo $total?>" />
                <input type="hidden" name="jform[day]" value="<?php echo $day?>" />
				<?php wp_nonce_field( 'hb_action', 'hb_meta_nonce' );?>
        <div class="form-group" style="padding-top: 20px; padding-bottom: 20px;">
            <div class="text-center">
                
                <button class="btn btn-danger btn_next" type="button" onclick="return submitForm()">Xác nhận <i class="icon-double-angle-right icon-large"></i></button>
            </div>
        </div>
    </form>
</div>
<script>
jQuery(document).ready(function($){
	$('input[name="pay_method"]').click(function(){
			var payment =  $(this).val();
			jQuery.ajax({
                type: "POST",
                url: '<?php echo site_url() ?>/?hbaction=payment&task=getPaymentForm&element='+payment+'&hb_meta_nonce=<?php echo wp_create_nonce('hb_meta_nonce'); ?>',                                
                success: function(result) {
                    jQuery('#payment_description').html(result);
                },
                error: function() {
                    displayProcessingForm(0);
                    alert('<?php echo __('System error warn'); ?>');
                }
            });
	});
	$('#payment_method_<?php echo $default_plugin?>').trigger('click');
});
    function submitForm() {
        var form = jQuery("#frontForm");
        //var validator = form.validate();
        if (1) { //form.valid()){
            if (jQuery("input[name='pay_method']").is(":checked") == false) {
                alert("<?php echo __('Please choose a payment method') ?>");
                return false;
            }

            //form.submit();
            displayProcessingForm(1);
            displayProcessingForm(1);
            jQuery.ajax({
                type: "POST",
                url: '<?php echo site_url('/?hbaction=order&task=book') ?>',
                dataType: 'json',
                data: form.serialize(), // serializes the form's elements.
                success: function(result) {
                    jQuery('#order_id').val(result.order_id);
                    if (result.hasOwnProperty("status")) {
                        if (result.status == 1) {
                            window.location = result.url;
                        } else {
                            displayProcessingForm(0);
                            alert(result.error.msg);
                            return false;
                        }
                    } else {
                        displayProcessingForm(0);
                        alert('<?php echo __('System error warn'); ?>');
                    }
                },
                error: function() {
                    displayProcessingForm(0);
                    alert('<?php echo __('System error warn'); ?>');
                }
            });
        } else {
            //validator.focusInvalid();
        }
        return false;
    }

    function displayProcessingForm(enable) {
        if (enable) {
            jQuery('button').prop("disabled", true);
            jQuery('body').css("opacity", '0.5');
            jQuery('body').append('<img id="loading"  style="position: fixed;top:50%;left: 50%;width:100px;z-index:999;" src="<?php echo FVN_URL . 'assets/images/loading.gif' ?>"/>');
            // 		jQuery('#loading').show();
        } else {
            jQuery('button').prop("disabled", false);
            jQuery('body').css("opacity", '1');
            jQuery('#loading').remove();
        }
    }
</script>
<?php get_footer(); ?> 