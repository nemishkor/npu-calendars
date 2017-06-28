<?php
class Controller_Doc extends Controller{
	
	function __construct($registry){
		parent::__construct($registry);
	}
	
	function action_index(){
		$data = null;
		$this->view->generate('doc_view.php', 'template_view.php', $data);
	}
	
}
?>
