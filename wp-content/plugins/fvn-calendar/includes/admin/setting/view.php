<?php
/**
 *View display page setting
 */

class FvnAdminViewSetting extends FvnAdminView {
	protected $form;
	public function display($tpl = null) {
		
		HBImporter::libraries ( 'form' );
		$this->form = new HBForm ( 'setting' );
		//debug($this->form);
		$this->input = HBFactory::getInput();
		$layout = $this->getLayout();
		wp_enqueue_script ( 'jquery-validate', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js' );
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script( 'jquery-ui-tabs' );
		$this->activeTab ();
		switch ($layout){
			case 'checkout':				
				$this->plugins = HBList::getPaymentAvailPlugin(false,false);
				//default is cash payment
// 				debug($this->plugins);die;
				$this->gateway = $this->input->get('gateway','hbpayment_cash');
				$instance = get_option ( $this->gateway, array () );
				$instance = json_decode ( $instance );
				$this->item = ( array ) $instance;
				//loading setting form if it is not cash payment
				foreach ($this->plugins as $plugin){
					if($plugin->name == $this->gateway){
						$this->form->loadFile ("{$plugin->file}{$this->gateway}.xml", true, '//config' );
					}					
				}
				
				// bind data to form
				$this->form->bind ( $this->item );
				
				break;
			default:
				$instance = get_option ( 'hb_params', array () );
				$instance = json_decode ( $instance );
// 				debug($instance);
				$this->item = ( array ) $instance;
				$this->form->loadFile ( FVN_PATH . 'includes/admin/setting/config.xml', true, '//config' );
				// bind data to form
				$this->form->bind ( $this->item );
				break;
		}
		
		
		return parent::display ( $tpl );
	}
	
	
	
	public function activeTab() {
		$input = HBFactory::getInput();
		$active_tab = $input->getString('layout','');
		?>
		<h2 class="nav-tab-wrapper">
			<a href="?page=setting" class="nav-tab <?php echo $active_tab == '' ? 'nav-tab-active' : ''; ?>"><?php echo __('Setting','hb')?></a>
			<a href="?page=setting&layout=notify" class="nav-tab <?php echo $active_tab == 'notify' ? 'nav-tab-active' : ''; ?>"><?php echo __('Email setting')?></a>
			<a href="?page=setting&layout=checkout" class="nav-tab <?php echo $active_tab == 'checkout' ? 'nav-tab-active' : ''; ?>"><?php echo __('Checkout','hb')?></a>
		</h2>
		<?php
			
	}

	public function emailTab() {
		$input = HBFactory::getInput();
		$active_tab = $input->getString('template','customer');
		HBImporter::helper('email');
		?>
		<h2 class="nav-tab-wrapper">
			<a href="?page=setting&layout=notify&template=customer" class="nav-tab <?php echo $active_tab == 'customer' ? 'nav-tab-active' : ''; ?>">Gửi khách hàng</a>
			<a href="?page=setting&layout=notify&template=payment" class="nav-tab <?php echo $active_tab == 'payment' ? 'nav-tab-active' : ''; ?>">Mail Thanh toán(chuyển khoản)</a>
			<a href="?page=setting&layout=notify&template=admin" class="nav-tab <?php echo $active_tab == 'admin' ? 'nav-tab-active' : ''; ?>">Gửi Admin</a>
			<a href="?page=setting&layout=notify&template=new_drawrequest" class="nav-tab <?php echo $active_tab == 'new_drawrequest' ? 'nav-tab-active' : ''; ?>">Yêu cầu rút tiền</a>
			<a href="?page=setting&layout=notify&template=approve_drawrequest" class="nav-tab <?php echo $active_tab == 'approve_drawrequest' ? 'nav-tab-active' : ''; ?>">Rút tiền thành công</a>
			<a href="?page=setting&layout=notify&template=reject_drawrequest" class="nav-tab <?php echo $active_tab == 'reject_drawrequest' ? 'nav-tab-active' : ''; ?>">Rút tiền thất bại</a>
		</h2>
		<div><b>Một số tag: </b>
		<?php $keys = FvnMailHelper::getOrderKey();
		foreach($keys as $k){echo '{'.$k.'} ';}?>
		{order_days}{package_name}{bank_name} {bank_number} {user_name} {link} {revenue} {total_revenue}</div>
		<?php
			
	}
}
$view = new FvnAdminViewSetting ();
$view->display ();
?>
