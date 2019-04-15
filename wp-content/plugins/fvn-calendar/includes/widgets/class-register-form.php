<?php 
if (! defined ( 'ABSPATH' )) {
	exit ();
}

class HbRegisterForm_Widget extends WP_Widget {
	public function __construct() {
	
		$widget_ops = array( 
			'classname' => 'HbRegisterForm_Widget',
			'description' => 'Form to client register email',
		);
		parent::__construct( 'HbRegisterForm_Widget', 'HBPRO Register Email Form', $widget_ops );
	}
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		foreach($new_instance as $key => $val){
			$instance[$key] = $val;
		}		
		return $instance;
	}
	public function form( $instance ) {?>
		<p><label for="<?php echo $this->get_field_id( 'class' ); ?>"><?php _e( 'Addition class:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" type="text" value="<?php echo $instance['class']; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e( 'Allow name' ); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>">
		<option value="1" <?php echo $instance['name']==1 ? 'selected="selected"' : ''?>>YES</option>
		<option value="0" <?php echo $instance['name']=='0' ? 'selected="selected"' : ''?>>NO</option>		
		</select>
		</p>
		<p><label for="<?php echo $this->get_field_id( 'mobile' ); ?>"><?php _e( 'Allow mobile' ); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id( 'mobile' ); ?>" name="<?php echo $this->get_field_name( 'mobile' ); ?>">
		<option value="1" <?php echo $instance['mobile']==1 ? 'selected="selected"' : ''?>>YES</option>
		<option value="0" <?php echo $instance['mobile']=='0' ? 'selected="selected"' : ''?>>NO</option>		
		</select>
		</p>
		<p><label for="<?php echo $this->get_field_id( 'request' ); ?>"><?php _e( 'Allow request field' ); ?></label>		
		<select class="widefat" id="<?php echo $this->get_field_id( 'request' ); ?>" name="<?php echo $this->get_field_name( 'request' ); ?>">
		<option value="1" <?php echo $instance['request']==1 ? 'selected="selected"' : ''?>>YES</option>
		<option value="0" <?php echo $instance['request']=='0' ? 'selected="selected"' : ''?>>NO</option>		
		</select>
		</p>
	<?php }
	
	public function widget( $args, $instance ) {
		?>
		<div class="hbwidget-register-form <?php echo $instance['class']?>">
				<div class="" style="padding:20px;">
					<div class="form-horizontal">
						<?php if($instance['name']){?>
						<div class="form-group">
							<input type="text" class="form-control" required id="hb_widget_register_name"  placeholder="Tên của bạn">
						</div>
						<?php }?>
						<div class="form-group">
							<input type="email" class="form-control" required id="hb_widget_register_email"  placeholder="Email của bạn">
						</div>
						<?php if($instance['mobile']){?>
						<div class="form-group">
							<input type="text" class="form-control" required id="hb_widget_register_phone"  placeholder="Số điện thoại của bạn">
						</div>
						<?php }?>
						<?php if($instance['request']){?>
						<div class="form-group">
							<textarea type="email" rows="5" class="form-control" required id="hb_widget_register_notes"  placeholder="Bạn có câu hỏi nào không, nếu không có vui lòng bỏ trống"></textarea>
						</div>
						<?php }?>
						
						<center><button type="button" id="hb_widget_register_confirm" class="btn btn-lg btn-primary">Đăng kí</button></center>
					</div>
				</div>
		</div>
		
			
		<script> 
			jQuery(document).ready(function($){
				
				$('#hb_widget_register_confirm').click(function(){
					var email = $('#hb_widget_register_email').val();
					var phone = $('#hb_widget_register_phone').val();
					var notes = $('#hb_widget_register_notes').val();
					var name = $('#hb_widget_register_name').val();
					var check=false;
					var valid = false;
					if(email.match(/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i)){
						check = true;
					}else{
						valid = 'email';
					}		
					if(phone.match(/^[(]{0,1}[0-9]{3}[)]{0,1}[-\s\.]{0,1}[0-9]{3}[-\s\.]{0,1}[0-9]{3,6}$/)){
						check=true;
					}else{
						valid = 'số điện thoại';
					}
					
					if(check){
						$.ajax({
							url: '<?php echo site_url('index.php?fvnaction=user&task=ajax_register&hb_meta_nonce='.wp_create_nonce( 'hb_meta_nonce' ));?>&email='+email+'&phone='+phone+'&notes='+notes+'&name='+name,
							type: "GET",
							dataType: "json",
							beforeSend: function(){
								display_processing_form(1);
							},
							success : function(result) {
								jAlert('Cám ơn bạn đã đăng kí chúng tôi sẽ gọi cho bạn sớm nhất có thể!<br>Chúc bạn một ngày tốt lành!');
								display_processing_form(0);
							},
							error: function(jqXHR, textStatus, errorThrown) {
								jAlert('Xin lỗi bạn, đã có lỗi xảy ra vui lòng thử lại hoặc gọi cho chúng tôi để được tư vấn ngay!');
								display_processing_form(0);
							}
						});
					}else{
						$('#input_contact_phones').focus();
						if(phone == '' && email==''){
							jtrigger_error('Bạn vui lòng nhập số điện thoại hoặc email','');
						}else{
							jtrigger_error('Số điện thoại hoặc email không đúng. Bạn vui lòng nhập lại nhé!','');
						}
						
					}
				});
			});
		</script>
		<?php 
	}
	
}