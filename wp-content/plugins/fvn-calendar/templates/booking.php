<?php 
FvnImporter::model('orders');
FvnImporter::helper('currency', 'date');

$input = HBFactory::getInput();
if($input->getInt('order_id')){
	$order = (new FvnModelOrders())->getComplexItem($input->getInt('order_id'));
    $total = $order->order->total;
}else{
    $total = $input->getInt('total');
}
// debug($order);
add_filter('pre_get_document_title', function () {
	return 'Đặt lịch hẹn ' . $package->name;
});
// wp_enqueue_style( 'visa', FVN_URL.'assets/css/visa.css', '', '1.0.0' );
$user = HBFactory::getUser();

$payment_plugin = HBList::getPaymentAvailPlugin();
$default_plugin = $input->get('payment_method',reset($payment_plugin)->name);
// debug($payment_plugin);die;
?>
<?php get_header(); ?>
<div class="container">
    <form id="frontForm" name="frontForm" action="<?php echo site_url()?>" method="POST">
        <h3>Đặt lịch hẹn</h3>

        <div class="content clearfix"></div>
        <hr>
        <div class="row">
            <div class="col-sm-3">Họ và tên</div>
            <div class="col-sm-9"><input class="form-control" required name="jform[name]" /></div>
        </div>
        <div class="row">
            <div class="col-sm-3">Điện thoại liên hệ</div>
            <div class="col-sm-9"><input type="tel" required class="form-control" name="jform[phone]" /></div>
        </div>
        <div class="row">
            <div class="col-sm-3">Giới tính</div>
            <?php echo FvnHtml::select(FvnParamGender::getAll(),'jform[gender]','required class="form-control','value','display',FvnParamGender::MALE['value'],'type')?>
        </div>
        <div class="row">
            <div class="col-sm-3">Chọn ngày</div>
            <div class="col-sm-9"><?php FvnHtml::calendar('','jform[start]','date',FvnDateHelper::getConvertDateFormat(),'class="form-control" required')?></div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-6">Chọn thời gian bắt đầu</div>
                    <div class="col-sm-6"><?php echo FvnHtml::timePicker('jform[start_time]','class="" style="width:100px"','','start_time')?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-6">Chọn thời gian kết thúc</div>
                    <div class="col-sm-6"><?php echo FvnHtml::timePicker('jform[end_time]','class="" style="width:100px"','','end_time')?></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">Chọn loại hình liên hệ</div>
            <div class="col-sm-9">
                <?php echo FvnHtml::select(FvnParamVideoCallType::getAll(),'jform[type]','required class="form-control','value','display','','type','Chọn cách chúng tôi liên hệ với bạn')?>
                <div id="type_desc" class="content"></div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-3">Chú ý</div>
            <div class="col-sm-9"><textarea class="form-control" rows="15" name="data[notes]"></textarea></div>
        </div>

        <br>
        <!-- <h3><?php echo __('Chọn phương thức thanh toán') ?></h3> -->

        <div id="payment-area" style="display:none">
							<?php foreach($payment_plugin as $p){?>
								<p>
								<input type="radio" id="payment_method_<?php echo $p->name?>" name="pay_method" value="<?php echo $p->name?>"/> <?php echo $p->params->display_name?>          
							</p>
							<?php }?>
        </div>
				<!-- <pre id="payment_description"></pre> -->
        <input type="hidden" name="jform[order_id]" value="<?php echo $input->getInt('order_id') ?>" />
        <input type="hidden" name="hbaction" value="order" />
        <input type="hidden" name="task" value="book" />
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
    // $('#payment_method_<?php echo $default_plugin?>').trigger('click');
    $('#type').change(function(){
        var social = $(this).val();
        var html = '';
        if(social=='<?php echo FvnParamVideoCallType::ZALO['value']?>'){
            html .= 'Vui lòng điền số điện thoại bạn đang sử dụng zalo, chúng tôi sẽ liên hệ với bạn qua nick Zalo này<br>';
            html .= '<input type="tel" name="jform[type_desc]" id="input_type_desc" class="form-control" required placeholder="Số điện thoại Zalo" />';
        }
        if(social=='<?php echo FvnParamVideoCallType::FACEBOOK['value']?>'){
            html .= 'Vui lòng điền link facebook trang cá nhân của bạn chúng tôi sẽ liên hệ với bạn qua nick facebook đó.<br>';
            html .= 'Đường link facebook của bạn sẽ có dạng https://facebook.com/xxx Trong đó xxx là nick của bạn. Vui lòng vào trang cá nhân của bạn và copy đường dẫn trên thanh địa chỉ trình duyệt.<br>';
            html .= 'Bạn có thể tham khảo <a href="https://trangcongnghe.com/thu-thuat/138787-cach-lay-link-facebook-tren-dien-thoai.html">tại đây</a> để được hướng dẫn cụ thể hơn.';
            html .= '<input type="text" name="jform[type_desc]" id="input_type_desc" class="form-control" required placeholder="https://facebook.com/xxx" value="https://facebook.com/"/>';
        }
        if(social=='<?php echo FvnParamVideoCallType::SKYPE['value']?>'){
            var popup = '';
            popup .= '<div class="row">';
            popup .= '<div class="col-md-3"><img class="avatar" src="'+fvn_url+'assets/images/skype-buoc1.jpg"/></div>';
            popup .= '<div class="col-md-3"><img class="avatar" src="'+fvn_url+'assets/images/skype-buoc2.jpg"/></div>';
            popup .= '</div>';

            html .= 'Vui lòng điền địa chỉ Skype của bạn <a href="javascript:void(0)" onclick="jAlert(popup)">Hướng dẫn</a><br>';
            html .= '<input type="text" name="jform[type_desc]" id="input_type_desc" class="form-control" required placeholder="Tên skype"/>';
            
        }
        $('#type_desc').html(html);
    });

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
                            jAlert(result.error.msg);
                            return false;
                        }
                    } else {
                        displayProcessingForm(0);
                        jAlert('<?php echo __('System error warn'); ?>');
                    }
                },
                error: function(e) {
                    displayProcessingForm(0);
                    jAlert(e);
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