<?php


class FvnAdminViewCountry extends FvnAdminView{
	public $items;
	
	public function display($tpl=null){
		if($this->input->get('layout') == 'edit'){
			$this->item = $this->get('Item');
		}else{
			$this->items = $this->get('Items');
			$this->pagination = $this->get('Pagination');
		}
		parent::display($tpl);
	}
	
}

$view = new FvnAdminViewCountry();
$view->display();
?>
