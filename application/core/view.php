<?php
class View
{
	private $registry;
	public $template_view = 'template_view.php';
	
	function __construct($registry){
		$this->registry = $registry;
	}
	
	function generate($content_view, $template_view = "template_view.php", $data = null)
	{
		/** @noinspection PhpIncludeInspection */
		include 'application/views/' . $template_view;
	}
	
	function widget($name, $params = null){
		$name = strtolower($name);
		$widget_file = 'application/widgets/' . $name . '.php';
		if(file_exists($widget_file)){
			/** @noinspection PhpIncludeInspection */
			include($widget_file);
			$widget_name = 'Widget_' . ucfirst($name);
			$widget = new $widget_name($params, $this->registry);
			return $widget->display();
		}
	}
}
?>
