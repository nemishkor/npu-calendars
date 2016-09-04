<?php

class Route
{
	private $registry;
	public $folder, $action_name, $action, $controller_name, $model_name;
	
	function __construct($registry) {
		$this->registry = $registry;
		$this->registry->set('route', $this);
		$this->registry->set('host', 'http://'.$_SERVER['HTTP_HOST'].'/');
		$this->start();
	}
	
	function start()
	{
		$folder = 'korenev/';
		// default controller
		$controller_name = 'Schedules';
		
		$routes = explode('/', str_replace($folder, '', $_SERVER['REQUEST_URI']));
		
		if ( !empty($routes[1]) )
		{	
			$controller_name = $routes[1];
		}
		
		$action_name = 'index';
		if ( !empty($routes[2]) )
		{
			if(strstr($routes[2], '?'))
				$action_name = strstr($routes[2], '?', true);
			else
				$action_name = $routes[2];
		}

		$model_name = 'Model_'.$controller_name;
		$this->registry->set('controller_name', $controller_name);
		$controller_name = 'Controller_'.$controller_name;
		$this->registry->set('action_name', $action_name);
		$action_name = 'action_'.$action_name;

		$model_file = strtolower($model_name).'.php';
		$model_path = "application/models/".$model_file;
		if(file_exists($model_path))
		{
			include "application/models/".$model_file;
		}

		$controller_file = strtolower($controller_name).'.php';
		$this->registry->set('controller_file', $controller_file);
		$controller_path = "application/controllers/".$controller_file;
		$this->registry->set('controller_path', $controller_path);
		if(file_exists($controller_path))
		{
			include "application/controllers/".$controller_file;
		}
		else
		{
			Route::ErrorPage404();
		}
		
		$this->registry->set('action', $action_name);
		$controller = new $controller_name($this->registry);
	
	}
	
	function ErrorPage404()
	{
        header('HTTP/1.1 404 Not Found');
		header("Status: 404 Not Found");
		header('Location:'.$this->registry['host'].'404.php');
		echo '<h1>404</h1>';
    }
}
?>
