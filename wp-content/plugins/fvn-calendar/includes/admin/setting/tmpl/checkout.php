<?php

?>
<!-- LIST OF PAYMENT -->
<div id="tabs">
	<ul class="subsubsub">
		
		<?php foreach ($this->plugins as $plugin){?>
			<li><a <?php if($this->gateway == $plugin->name){echo "style='color:red'";}?> href='<?php echo admin_url('admin.php?page=setting&layout=checkout&gateway='.$plugin->name)?>'><?php echo __($plugin->title,'hb')?></a>|</li>
		<?php }?>
	</ul>
</div>
<br>

<form action="<?php echo admin_url('admin.php')?>"
	method="post" name="adminForm" id="adminForm" class="form-validate adminForm">
	<!-- CONFIG -->
	<?php echo $this->form->renderFieldSet('basic',array('name'=>'params[%s]'));?>
	
	<?php wp_nonce_field( 'hb_action', 'hb_meta_nonce' );?>
	<input type="hidden" name="fvnaction" value="setting" />
	<input type="hidden" name="gateway" value="<?php echo $this->gateway?>" />
	<input type="hidden" id="task" name="task" value="savegateway" />
	<?php  submit_button()?>
</form>