<?php
require_once dirname(__DIR__) . '/core/google.php';

class Controller {
	
	public $registry;
	public $google;
	public $model;
	public $view;
	public $default_view;
	
	function __construct($registry)
	{
		$this->registry = $registry;
		$this->registry->set('controller', $this);
		$default_view = strtolower($this->registry['controller_name']) . '_view.php';
		
		
		$this->google = new Google($this->registry, 'users');
		$this->registry->set('google', $this->google);

		$this->view = new View($this->registry);
		$this->registry->set('view', $this->view);
		if(method_exists($this, $this->registry['action']))
		{
			$have_permission = $this->google->check_permission();
			if($have_permission){
				$action_name = 'action_' . $this->registry['action_name'];
				$this->$action_name();
			} else {
				$group_id = 4;
				$permissions = $this->google->get_group_permissions($group_id);
				$this->view->generate('access_denied.php', 'template_view.php', null);
			}	
		}
		else
		{
			$route = $this->registry['route'];
			$route::ErrorPage404();
		}
	}
	
	function action_index(){
	}
}
?>
