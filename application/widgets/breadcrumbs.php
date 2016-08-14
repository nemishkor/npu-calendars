<?php
class Widget_Breadcrumbs extends Widget{
	
	function display(){
		if($this->registry['action_name'] != 'index')
			echo '<a href="/' . $this->registry['controller_name'] . '"><i class="uk-icon-arrow-circle-left"></i> Назад</a>';
	}
	
}
?>
