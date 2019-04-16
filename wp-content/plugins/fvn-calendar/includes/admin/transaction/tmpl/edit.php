<?php
FvnImporter::helper('params');
//debug($this->item);
$query = HBFactory::getQuery();
$query->select('*')->from('#__users');
global $wpdb;
$users = $wpdb->get_results($query->__toString());
?>
<h3><?php echo __('transactions')?></h3>
<div class="container">
	<form id="adminForm" action="<?php echo admin_url('admin.php?action=fvnaction&fvnaction=transaction&task=save')?>" method="post">
		<div class="">
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('User')?><span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<?php echo FvnHtml::select($users,'data[user_id]','','ID','display_name')?>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Số tiền')?><span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<input class="form-control input-medium required name" required type="number" id="price_tour"
						name="data[total]" value="<?php echo $this->item->total?>"/>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Nội dung')?></label>
				<div class="col-sm-9">
					<textarea name="data[content]" rows="5"><?php echo $this->item->content?></textarea>
				</div>
			</div>
			
			
			
			<input type="hidden" value="<?php echo $this->input->get('id')?>" name="id"/>
			
			
		</div>
		<?php wp_nonce_field( 'hb_action', 'hb_meta_nonce' );?>
		<input type="hidden" name="task" value="save"/>
		<!-- <center><button type="submit" class="btn btn-primary btn-lg"><?php echo __('Save')?></button></center> -->
		<center><button type="button" onclick="hb_submit_form('save_and_close')" class="btn btn-primary btn-lg"><?php echo __('Save and close')?></button></center>
	</form>
	
</div><!-- #primary -->
