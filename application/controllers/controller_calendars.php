<?php
class Controller_Calendars extends Crud_Controller
{
	function action_index()
	{
		if(!empty($_POST['created_by']))
			$user_id = $_POST['created_by'];
		else {
			$user = $this->google->get_user();
			$user_id = $user['id'];
		}
		$data = $this->model->get_data($user_id);
		$this->view->generate($this->view_file_name, 'template_view.php', $data);
	}

	function action_edit(){
		// saving
		if(isset($_POST['action'])){
		    $item_id = null;
			if($_POST['id'])
				$item_id = $this->model->save();
			else
				$item_id = $this->model->create();
			header('Location:' . $this->registry['host'] . strtolower($this->registry['controller_name']) . '/edit?id=' . $item_id);
		} else {
			if(isset($_GET['id']) && $_GET['id'])
				$data = $this->model->get_edit_item($_GET['id']);
			else
			    $data = $this->model->get_edit_item();
			$this->view->generate($this->view_file_name, 'template_view.php', $data);
		}
	}	
	
	function action_view(){
		if(!(isset($_GET['id']) && $_GET['id']))
			$this->registry->set('error', 'You must choise calendar for view them');
		$data = $this->model->get_view_item($_GET['id']);
		$this->view->generate($this->view_file_name, 'template_view.php', $data);
	}
	
	function action_add_to_google(){
		$data = array();
		if(isset($_GET['id']) && $_GET['id']){
			$calendar = $this->model->get_calendar($_GET['id']);
			$g_calendar_name = ($calendar['name'] == '') ? 'Немає імені' : $calendar['name'];
			$google = $this->registry['google'];
			$service = new Google_Service_Calendar($google->client);
			$g_calendar = new Google_Service_Calendar_Calendar();
			$g_calendar->setSummary($g_calendar_name);
			$g_calendar->setTimeZone('Europe/Kiev');
			$createdCalendar = $service->calendars->insert($g_calendar);
			$this->registry->set('debug', $createdCalendar);
		}
		$this->view->generate($this->view_file_name, 'template_view.php', $data);
	}
	
}
?>
