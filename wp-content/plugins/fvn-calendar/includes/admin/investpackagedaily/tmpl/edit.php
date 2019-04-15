<?php
HBImporter::helper('params');

// debug($this->item);
?>
<h3>Gói đầu tư</h3>
<div class="container">
	<form id="adminForm" action="<?php echo admin_url('admin.php?action=fvnaction&fvnaction=investpackage&task=save')?>" method="post">
		<div class="">
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Tên gói đầu tư')?><span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<input class="form-control input-medium required name" required type="text" id="name"
						name="data[name]" value="<?php echo $this->item ? $this->item->name : ''?>"/>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Số tiền tối thiểu')?><span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<input class="form-control input-medium required name" required type="text" id="min_price"
						name="data[min_price]" value="<?php echo $this->item->min_price?>"/>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Loại lãi xuất')?><span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<?php echo FvnHtml::select(FvnParamInvestType::getAll(),'data[type]','','value','display',$this->item->type,'data_type')?>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Lãi xuất')?>%<span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<div id="lai_xuat"></div>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Description')?></label>
				<div class="col-sm-9">
					<?php echo FvnHtml::editor('data[description]',[],$this->item->description,'data_description')?>
				</div>
			</div>
			
			
			
			<input type="hidden" value="<?php echo $this->input->get('id')?>" name="id"/>
			
			
		</div>
		<?php wp_nonce_field( 'hb_action', 'hb_meta_nonce' );?>
		<input type="hidden" name="task" value="save"/>
		<center><button type="submit" class="btn btn-primary btn-lg"><?php echo __('Save')?></button></center>
		<center><button type="button" onclick="hb_submit_form('save_and_close')" class="btn btn-primary btn-lg"><?php echo __('Save and close')?></button></center>
	</form>

<script>
var rate_val = <?php echo json_encode($this->item->rate)?>;
jQuery(document).ready(function($){
	$('#data_type').change(function(){
		var html = '';
		if($(this).val()=='<?php echo FvnParamInvestType::MONTHLY['value']?>'){
			html += '3 tháng: <input name="data[rate][3]" value="'+(rate_val[3]!=undefined?rate_val[3]:'')+'"><br>';
			html += '6 tháng: <input name="data[rate][6]" value="'+(rate_val[6]!=undefined?rate_val[6]:'')+'"><br>';
			html += '12 tháng: <input name="data[rate][12]" value="'+(rate_val[12]!=undefined?rate_val[12]:'')+'"><br>';
			html += '> 12 tháng: <input name="data[rate][13]" value="'+(rate_val[13]!=undefined?rate_val[13]:'')+'"><br>';
		}else{
			html += '<input class="form-control input-medium required name" required type="text" name="data[rate][]" value="'+(rate_val[0]!=undefined?rate_val[0]:'')+'"/>';
		}
		$('#lai_xuat').html(html);
	});
	$('#data_type').trigger('change');
});
</script>
	
</div><!-- #primary -->
