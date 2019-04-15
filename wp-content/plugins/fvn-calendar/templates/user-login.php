
<?php
HBImporter::helper('math', 'invest','html','date');
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
<div class="container">
    <div class="row">
		<div class="col-md-3"></div>
        <div class="col-md-6 mt-5 col-md-offset-3 pt-2 mb-5 register-form">
            <center><h2>Đăng Nhập</h2></center>
            <form method="post" action="<?php echo site_url('index.php?hbaction=user&task=login')?>">
                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-3 col-form-label">Email</label>
                    <div class="col-sm-9"><input type="text" name="data[email]" class="form-control" required placeholder="Email"></div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword" class="col-sm-3 col-form-label">Mật khẩu</label>
                    <div class="col-sm-9">
                        <input type="password" name="data[password]" class="form-control" required id="inputPassword" placeholder="Password">
                    </div>
                </div>
                
                <div class="mb-2 mt-2 clearfix">
                    <div><input type="checkbox" name="data[remember]"/> Nhớ mật khẩu</div>
                </div>
				<div class="mb-2 mt-2 clearfix">
                    <a href="<?php echo site_url('/register-page')?>">Đăng kí tài khoản mới</a>
                </div>
				<input type="hidden" name="redirect" value="<?php echo $_GET['redirect']?>"/>
                <?php wp_nonce_field( 'hb_action', 'hb_meta_nonce' );?>
                <center><button type="submit" class="btn btn-primary mb-2">Đăng nhập</button></center>
            </form>
        </div>
    </div>
    
</div>
<?php get_footer() ?> 