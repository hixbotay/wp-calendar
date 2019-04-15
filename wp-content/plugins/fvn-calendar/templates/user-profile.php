<?php
HBImporter::model('orders');
HBImporter::helper('math', 'invest','date','currency');
FvnHtml::add_datepicker_lib();
$user = HBFactory::getUser();
 //debug($user);
FvnHelper::checkLogin();

add_filter('pre_get_document_title',function(){return 'Thông tin cá nhân';});
get_header();
$orders = (new FvnModelOrders())->getOrderByUser($user->id,array(['success'=>1]));
$total=0;
$total_revenue = 0;
foreach($orders as $o){
    $total += $o->total;
    $total_revenue += FvnInvestHelper::caculateDrawAble($o)['revenue'];
}
?>
<div class="container">
    <?php echo FvnHelper::renderLayout('user-sidebar', array('layout' => 'profile')) ?>
    <div class="row">
        <div class="col-md-6"><h2>Thông tin cá nhân</h2></div>
        <div class="col-md-6" style=""><div class="pull-right" style="color:red;"><h2>Tổng số lợi nhuận: <?php echo FvnCurrencyHelper::displayPrice($total)?> + <?php echo FvnCurrencyHelper::displayPrice($total_revenue)?></h2></div></div>
    </div>
    <form action="<?php echo site_url('index.php')?>" method="post">
        <div class="form-group row">
            <label for="staticEmail" class="col-sm-3 col-form-label">Email</label>
            <div class="col-sm-9"><?php echo $user->data->user_email ? $user->data->user_email : '<input type="email" name="data[email]" class="form-control" id="email" placeholder="Email">'?></div>
        </div>
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-3 col-form-label">Password</label>
            <div class="col-sm-9">
                <input type="password" name="data[password]" class="form-control" id="inputPassword" placeholder="Password">
            </div>
		</div>
		<div class="form-group row">
            <label for="inputPassword" class="col-sm-3 col-form-label">Xác nhận mật khẩu</label>
            <div class="col-sm-9">
                <input type="password" name="data[confirm_password] class="form-control" placeholder="Password">
            </div>
		</div>
		
		<div class="form-group row">
            <label for="inputPassword" class="col-sm-3 col-form-label">Họ và tên<br><span style="font-size:smaller">(Họ và tên phải trùng với tài khoản ngân hàng để có thể rút tiền)</span></label>
            <div class="col-sm-9">
                <input type="text" name="data[display_name]" class="form-control" value="<?php echo $user->data->display_name?>" />
            </div>
		</div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Ngày sinh<span class="text-danger">*</span></label>
            <div class="col-sm-9">
                    <?php echo FvnHtml::calendar($user->birthday, 'data[birthday]','birthday','yy-mm-dd','readonly class="form-control input-medium required name" required',array('changeMonth'=>true,'changeYear'=>true))?>
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Giới tính<span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input class="required" required type="radio" id="gender_m"
                    name="data[gender]" value="M" <?php echo FvnHtml::checked($user->gender,['M'])?>/>Nam
                <input class="required" required type="radio" id="gender_f"
                    name="data[gender]" value="F" <?php echo FvnHtml::checked($user->gender,['F'])?>/>Nữ
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Số điện thoại<span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input class="form-control input-medium required" type="tel" maxlength="14" required id="user_phone"
                    name="data[phone]" value="<?php echo $user->phone?>" oninvalid="this.setCustomValidity('Số điện thoại định dạng không đúng')" />
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Địa chỉ<span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <textarea class="form-control input-medium required" required type="text" id="user_address" name="data[address]" rows="6"><?php echo $user->address?></textarea>
            </div>
        </div>
		<div class="form-group row">
            <label for="inputPassword" class="col-sm-3 col-form-label">Tên ngân hàng</label>
            <div class="col-sm-9">
                <input type="text" name="data[bank_name]" class="form-control" value="<?php echo $user->bank_name?>" />
            </div>
		</div>
		<div class="form-group row">
            <label for="inputPassword" class="col-sm-3 col-form-label">Số tài khoản ngân hàng</label>
            <div class="col-sm-9">
                <input type="text" name="data[bank_number]" class="form-control" value="<?php echo $user->bank_number?>" />
            </div>
		</div>
		<div class="form-group row">
            <label for="inputPassword" class="col-sm-3 col-form-label">Chi nhánh ngân hàng</label>
            <div class="col-sm-9">
                <input type="text" name="data[bank_address]" class="form-control" value="<?php echo $user->bank_address?>" />
            </div>
		</div>
        <input type="hidden" name="hbaction" value="user"/>
        <input type="hidden" name="task" value="update"/>
        <?php wp_nonce_field( 'hb_action', 'hb_meta_nonce' );?>
		<center><button type="submit" class="btn btn-primary mb-2">Lưu</button></center>
    </form>
</div>
<?php get_footer() ?> 