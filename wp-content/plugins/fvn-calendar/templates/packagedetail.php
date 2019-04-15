<?php 
HBImporter::model('investpackage');
HBImporter::helper('currency','date');
$input = HBFactory::getInput();
$id = FvnHelper::get_url_path()[1];
$package = (new FvnModelInvestPackage())->getItem($id);
if(!$package->id){
    hb_enqueue_message('Gói đầu tư không tìm thấy','error');
    wp_redirect(site_url('message'));
}

// debug($order);
add_filter('pre_get_document_title',function(){return 'Gói đầu tư '.$package->name;});
// wp_enqueue_style( 'visa', FVN_URL.'assets/css/visa.css', '', '1.0.0' );
$user = HBFactory::getUser();
?>
<?php get_header();?>

<section id="site-main">
	<form id="frontForm" action="<?php echo site_url('/payment')?>" method="get">
<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h3><?php echo $package->name?></h3>    
			<hr>
			</div>
			
		</div>
	<div class="row">
	
		<div class="col-md-6">
					
			<div class="row">
				<div class="col-sm-3">Lãi xuất</div>
				<div class="col-sm-9" id="lai_xuat">
					<?php if($package->type==FvnParamInvestType::MONTHLY['value']){?>
						<?php foreach($package->rate as $month=>$rate){
							$month = $month<12 ? "{$month} tháng" : ($month==12 ? "1 năm" : ">12 tháng");
							echo "{$month}: {$rate}%<br>";
						}?>
					<?php }else{
						echo reset($package->rate).'%';
					}?>
				</div>
			</div>       
			<div class="row">
				<div class="col-sm-3">Số tiền tối thiểu</div>
				<div class="col-sm-9"><?php echo FvnCurrencyHelper::displayPrice($package->min_price)?></div>
			</div>
			<hr>
		   <?php if($user->id){?>
			
			<div class="form-group row">
				<label for="inputPassword" class="col-sm-3 col-form-label">Số tiền bạn muốn đầu tư</label>
				<div class="col-sm-9">
					<input type="number" name="total" required class="form-value form-control" value="" min="<?php echo $package->min_price?>" />
				</div>
			</div>
			<div class="form-group row">
				<label for="inputPassword" class="col-sm-3 col-form-label">Thời gian</label>
				<div class="col-sm-9">
					<?php if($package->type == FvnParamInvestType::MONTHLY['value']){?>
						<select name="day" class="form-value ">
							<option value="90">3 tháng</option>
							<option value="180">6 tháng</option>
							<option value="365">1 năm</option>
						</select>
					<?php }?>
					<?php if($package->type == FvnParamInvestType::YEAR['value']){?>
						<select name="day" class="form-value ">
							<option value="365">1 năm</option>
							<option value="730">2 năm</option>
							<option value="1095">3 năm</option>
						</select>
					<?php }?>
					<?php if($package->type == FvnParamInvestType::DAY['value']){?>
						<input type="number" name="day" required class="form-value form-control" value="" min="1" />
					<?php }?>
					
				</div>
			</div>
			<div class="form-group row">
				<label for="inputPassword" class="col-sm-3 col-form-label">Lợi nhuận dự tính</label>
				<div class="col-sm-9">
					<div id="result_revenue">0đ</div>
				</div>
			</div>
			<div class="form-group row">
				<label for="inputPassword" class="col-sm-3 col-form-label">Lợi nhuận dự tính</label>
				<div class="col-sm-9">
				<div id="result_total">0đ</div>
				</div>
			</div>
			<input type="hidden" name="invest_package_id" value="<?php echo $package->id?>"/>
			
		   
		   <?php }?>
	   </div>
		<div class="col-md-6"><div class="content clearfix"><?php echo $package->description?></div></div>
	</div>
	<div class="clearfix" style="width:100%">
		<?php if($user->id){?>
			<center><button type="submit" class="btn btn-primary mb-2">Xác nhận</button></center>
		<?php }else{?>
			<center><a class="btn btn-primary" href="<?php echo site_url('login-page/?redirect='.base64_encode(site_url('/packagedetail/'.$package->id)))?>">Đăng nhập để đầu tư</a></center>
		<?php }?>
		
		
		
	</div>
    
</div>
</form>
</section>
<script>
jQuery(document).ready(function($){
    $('.form-value').change(function(){
        var form = $('#frontForm');
        jQuery.ajax({
                type: "POST",
                url: '<?php echo site_url('/?hbaction=order&task=ajax_caculate_revenue') ?>',
                dataType: 'json',
                data: form.serialize(), // serializes the form's elements.
                success: function(result) {
                    jQuery('#result_revenue').html(result.revenue);
                    jQuery('#result_total').html(result.total);
                },
                error: function() {
                    displayProcessingForm(0);
                    alert('<?php echo __('System error warn'); ?>');
                }
            });
    });
    // $('[name="day"]').trigger('change');
});
</script>
<?php get_footer(); ?>


