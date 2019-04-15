<?php
HBImporter::helper('params');
HBImporter::model('period');
//debug($this->item);
$periods = (new FvnModelPeriod())->getList();
// debug($periods);
$status = array(
		(object)array('value'=>1,'title'=>__('Allow')),
		(object)array('value'=>0,'title'=>__('Disable'))
);
// debug($this->item);
?>
<h3><?php echo __('Country')?></h3>
<div class="container">
	<form id="adminForm" action="<?php echo admin_url('admin.php?action=fvnaction&fvnaction=country&task=save')?>" method="post">
		<div class="">
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Country code')?><span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<input class="form-control input-medium required name" required type="text" id="name"
						name="data[country_code]" maxlength="150" value="<?php echo $this->item->country_code?>"/>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Country Name')?><span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<input class="form-control input-medium required name" required type="text" id="name"
						name="data[country_name]" maxlength="150" value="<?php echo $this->item->country_name?>"/>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Price')?><span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<table>
					<tr>
						<td><?php echo __('Period')?></td>
						<td><?php echo __('Price of Tour')?></td>
						<td><?php echo __('Price of Bussiness')?></td>
						<td><?php echo __('Status of period')?></td>
					</tr>
					<?php 
					foreach($periods as $p){
					$p_id = $p->id;?>
						<tr>
							<td><?php echo $p->name?></td>
							<td><input name="data[params][<?php echo $p->id?>][tour]" value='<?php echo $this->item->params->$p_id->tour?>' /></td>
							<td><input name="data[params][<?php echo $p->id?>][bus]" value='<?php echo $this->item->params->$p_id->bus?>' /></td>
							<td><?php echo FvnHtml::radio($status, 'data[params]['.$p->id.'][status]', '', 'value', 'title',$this->item->params->$p_id->status)?></td>
						</tr>
					<?php }?>
					</table>
				</div>
			</div>
			
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __('Description notice when select the country')?></label>
				<div class="col-sm-9">
					<?php wp_editor( $this->item->description, 'data_description', array('textarea_name'=>'data[description]') );?>
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
