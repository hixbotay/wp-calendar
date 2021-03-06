<?php
$mail_option = get_option('fvn_mail_payment');
$mail_option = json_decode($mail_option);
?>
<form action="<?php echo admin_url('admin.php?action=fvnaction')?>"
	method="post" name="adminForm" id="adminForm" class="form-validate adminForm">
	<!-- CONFIG -->
	<table style="width:100%">
		<tr>
			<td>From Name</td>
			<td><?php echo FvnHtml::text('data[from_name]','',$mail_option->from_name)?></td>
		</tr>
		<tr>
			<td>From Email</td>
			<td><?php echo FvnHtml::mail('data[from_email]','',$mail_option->from_email)?></td>
		</tr>
		<tr>
			<td>Tiêu đề Email</td>
			<td><?php echo FvnHtml::text('data[title]','',$mail_option->title)?></td>
		</tr>
		<tr>
			<td>Nội dung</td>
			<td><?php echo FvnHtml::editor('data[description]',[],$mail_option->description,'fvn_mail_payment_description')?></td>
		</tr>
		
	</table>
	
	<?php wp_nonce_field( 'hb_action', 'hb_meta_nonce' );?>
	<input type="hidden" name="fvnaction" value="setting" />
	<input type="hidden" id="task" name="task" value="saveMail" />
	<input type="hidden" id="template" name="template" value="<?php echo $this->input->get('template')?>" />
	<?php  submit_button()?>
</form>