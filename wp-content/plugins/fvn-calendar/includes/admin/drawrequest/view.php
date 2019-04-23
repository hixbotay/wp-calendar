<?php


class FvnAdminViewDrawRequest extends FvnAdminView{
	public $items;
	
	public function display($tpl=null){
		FvnImporter::model('orders');
		$this->items = (new FvnModelOrders())->getBooked();
		parent::display($tpl);
	}
	
}

$view = new FvnAdminViewDrawRequest();
$view->display();
?>
