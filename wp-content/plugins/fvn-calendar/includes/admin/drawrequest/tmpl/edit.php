<?php
FvnImporter::helper('params');
//debug($this->item);
?>
<h3><?php echo __('drawrequests')?></h3>
<div class="container">
	<form id="adminForm" action="<?php echo admin_url('admin.php?action=fvnaction&fvnaction=drawrequest&task=save')?>" method="post">
		<div class="">
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('drawrequest name')?><span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<input class="form-control input-medium required name" required type="text" id="name"
						name="data[name]" maxlength="150" value="<?php echo $this->item ? $this->item->name : ''?>"/>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Default price for touris')?><span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<input class="form-control input-medium required name" required type="text" id="price_tour"
						name="data[price_tour]" maxlength="150" value="<?php echo $this->item->price_tour?>"/>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Default price for Bussiness')?><span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<input class="form-control input-medium required name" required type="text" id="price_bus"
						name="data[price_bus]" maxlength="150" value="<?php echo $this->item->price_bus?>"/>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Description')?></label>
				<div class="col-sm-9">
					<textarea name="data[description]" rows="5"><?php echo $this->item->description?></textarea>
				</div>
			</div>
			
			
			
			<input type="hidden" value="<?php echo $this->input->get('id')?>" name="id"/>
			
			
		</div>
		<?php wp_nonce_field( 'hb_action', 'hb_meta_nonce' );?>
		<input type="hidden" name="task" value="save"/>
		<center><button type="submit" class="btn btn-primary btn-lg"><?php echo __('Save')?></button></center>
		<center><button type="button" onclick="hb_submit_form('save_and_close')" class="btn btn-primary btn-lg"><?php echo __('Save and close')?></button></center>
	</form>
	
</div><!-- #primary -->
