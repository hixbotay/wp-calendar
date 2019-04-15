
<?php echo $this->emailTab();
$template = 'notify-'.$this->input->get('template','customer').'.php';
include $template;?>