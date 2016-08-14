<?php
class Controller_User extends Crud_Controller{	
	
	function action_index(){	
		$user = $this->google->get_user();
		if($user){
			$data = $this->model->get_data($user['id']);
		    $this->view->generate($this->view_file_name, 'template_view.php', $data);
		} else {
			$this->view->generate('user_login_view.php', 'template_view.php', $data);
		}
	}
	
	function action_login(){
		$data = $this->google->login();
		$this->view->generate('calendars_login.php', 'template_view.php', $data);
	}
	
	 
	function action_edit(){
		if($_POST['action']){
			$item_id = $this->model->save();
			if($_POST['action'] == 'close'){
				header('Location:' . $this->registry['host'] . strtolower($this->registry['controller_name']) . '/index');
			}
		}
		$user = $this->google->get_user();
		$data = $this->model->get_data($user['id']);
	    $this->view->generate($this->view_file_name, 'template_view.php', $data);
	}
	
}
?>
