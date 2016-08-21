<?php
class Controller_User extends Crud_Controller{	
	
	function action_index(){
		$google = $this->registry['google'];
		if(isset($_GET['logout'])) {
			if(!empty($_SESSION['access_token']))
				$google->client->revokeToken($_SESSION['access_token']);
			unset($_SESSION['access_token']);
		}
		if (!empty($_SESSION['access_token']) && isset($_SESSION['access_token']['id_token'])) {
			var_dump($_SESSION["access_token"]);
			$data = array();
			$data['user_data'] = $google->client->verifyIdToken();
			// db users
			$google_id = $data['user_data']['sub'];
			$email = $data['user_data']['email'];
			$hash = $_SESSION["access_token"]['access_token'];
			$is_new = !$this->model->check_user_exist($email);
			if($is_new)
				$this->model->register_user($google_id, $email, $hash);
			else
				$this->model->update_hash($email, $hash);
			$data['user'] = $this->model->get_user();
			$data['isNew'] = $is_new;
			$google->client->setAccessToken($_SESSION['access_token']);
		    $this->view->generate($this->view_file_name, 'template_view.php',  $data);
		} else {
			$this->view->generate('user_login_view.php');
		}
	}

	function action_login(){
		if (!empty($_SESSION['access_token']) && isset($_SESSION['access_token']['id_token'])) {
			$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/user/index';
			header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		} else {
			$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/user/oauth2callback';
			header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		}
	}

	function action_oauth2callback(){
		$google = $this->registry['google'];
		if (! isset($_GET['code'])) {
			$auth_url = $google->client->createAuthUrl();
			header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
		} else {
			$token = $google->client->authenticate($_GET['code']);
			$google->client->setAccessToken($token);
			$_SESSION['access_token'] = $token;
			$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/user/index';
			header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		}
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
