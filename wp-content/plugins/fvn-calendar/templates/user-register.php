<?php
FvnImporter::helper('math', 'invest','html','date');
FvnHtml::add_datepicker_lib();
$user = HBFactory::getUser();
if($user->id){
    wp_redirect(site_url('/profile'));
    exit;
}
add_filter('pre_get_document_title',function(){return 'Đăng nhập và đăng kí';});
get_header();
?>
<style>
	#main{background:#F6F7F7}
	.register-form{background:white;}
</style>
<div class="container well well-small">
    <div class="row ">
		<div class="col-md-3"></div>
        <div class="col-md-6 mt-5 col-md-offset-3 register-form pt-2 mb-5">
            <center><h2>Đăng kí tài khoản</h2></center>
				<form action="<?php echo site_url()?>/index.php?hbaction=user&task=register" method="post">
					<div class="">
						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Số điện thoại của bạn<span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input class="form-control input-medium required" type="tel" maxlength="14" required id="user_phone"
									name="user[phone]" oninvalid="this.setCustomValidity('Số điện thoại định dạng không đúng')" />
							</div>
						</div>
						<!--
                        <div class="form-group row">
							<label class="col-sm-4 col-form-label">Email<span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input class="form-control input-medium" type="email" id="user_email"
									name="user[user_email]"  />
							</div>
						</div>
						-->
						
						<!--
						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Nhập lại mật khẩu<span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input class="form-control input-medium" type="password" id="passwordconfirm"/>
							</div>
						</div>
						-->
						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Họ và tên<span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input class="form-control input-medium required name" required type="text" 
									name="user[display_name]" maxlength="150" />
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Mật khẩu<span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input class="form-control input-medium" type="password" id="user_password"
									name="user[user_pass]"  />
							</div>
						</div>
						<!--
						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Tên tài khoản<span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input class="form-control input-medium required name" required type="text" id="username"
									name="user[user_login]" maxlength="150" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Ngày sinh<span class="text-danger">*</span></label>
							<div class="col-sm-8">
									<?php echo FvnHtml::calendar('', 'user[birthday]','birthday','yy-mm-dd','readonly class="form-control input-medium required name" required',array('changeMonth'=>true,'changeYear'=>true))?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Giới tính<span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input class="required" required type="radio" id="gender_m"
									name="user[gender]" value="M" />Nam
								<input class="required" required type="radio" id="gender_f"
									name="user[gender]" value="F" />Nữ
							</div>
						</div>
						
						

						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Địa chỉ<span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<textarea class="form-control input-medium required" required type="text" id="user_address" name="user[address]" rows="6"></textarea>
							</div>
						</div>
						
						

                        <div class="form-group row">
							<label class="col-sm-4 col-form-label">Tên ngân hàng<span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input class="form-control input-medium required" type="text" maxlength="14" required id="bank_name"
									name="user[bank_name]"/>
							</div>
						</div>
                        <div class="form-group row">
							<label class="col-sm-4 col-form-label">Số tài khoản ngân hàng<span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input class="form-control input-medium required" type="text" maxlength="14" required id="bank_number"
									name="user[bank_number]"/>
							</div>
						</div>
                        <div class="form-group row">
							<label class="col-sm-4 col-form-label">Chi nhánh ngân hàng<span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input class="form-control input-medium required" type="text" maxlength="14" required id="bank_address"
									name="user[bank_address]"/>
							</div>
						</div>
						
						<input type="checkbox" id="term" required oninvalid="this.setCustomValidity('Vui lòng đọc và chấp nhận các điều khoản')"/> Tôi đã đọc và chập nhận tất cả <a targer="_blank" href="<?php echo site_url('dieu-khoan')?>">chính sách và điều khoản</a> của chúng tôi
						<div class="clearfix"></div>
						-->
						
					</div>
					<?php wp_nonce_field( 'hb_action', 'hb_meta_nonce' );?>
					<div class="clearfix" style="width:100%;">
						<button type="submit" class="btn btn-primary pull-left">Đăng kí</button>
						<div class="pull-right">Đã có tài khoản <a class="pull-right" href="<?php echo site_url('/login-page')?>">Đăng nhập</a></div>
					</div>
                </form>
        </div>
    </div>
    
</div>
<?php get_footer() ?> 