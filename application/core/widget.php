<?php
class Widget{
	protected $registry;
	protected $params;
	function __construct($params, $registry){
		$this->params = $params;
		$this->registry = $registry;
	}
	function display(){
		return 'this is widget ' . strstr('Widget_','',get_class($this));
	}
}
?>
