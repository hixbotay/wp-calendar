
<form action="<?php echo admin_url('admin.php?action=fvnaction')?>"
	method="post" name="adminForm" id="adminForm"
	class="form-validate adminForm">
	<!-- CONFIG -->
	<?php echo $this->form->renderFieldGroup(array('name'=>'params[%s]'));?>
	<!-- EMAIL -->
	<?php ?>
	<?php wp_nonce_field( 'hb_action', 'hb_meta_nonce' );?>
	<input type="hidden" name="fvnaction" value="setting" />
	<input type="hidden" id="task" name="task" value="save" />
	<?php  submit_button()?>
</form>
