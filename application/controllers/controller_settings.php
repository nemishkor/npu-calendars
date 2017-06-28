<?php
class Controller_Settings extends Controller
{	
	function __construct($registry)
	{
		$this->model = new Model_Settings($registry);
		$this->view = new View();
		parent::__construct($registry);
	}
		
	function action_index()
	{	
		$data = $this->model->get_data();
		$this->view->generate('settings_view.php', 'template_view.php', $data);
	}
	
}
?>
