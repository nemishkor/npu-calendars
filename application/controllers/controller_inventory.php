<?php
class Controller_Inventory extends Controller
{	
	function __construct($registry)
	{
		$this->model = new Model_Inventory();
		$this->view = new View();
		parent::__construct($registry);
	}
		
	function action_index()
	{	
		$data = $this->model->get_data();
		$this->view->generate('inventory_view.php', 'template_view.php', $data);
	}
	
}
?>
