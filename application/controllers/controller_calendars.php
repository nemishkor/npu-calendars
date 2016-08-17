<?php
class Controller_Calendars extends Crud_Controller
{
	function action_index()
	{
		if(isset($_GET['test'])) {
			echo 'test1';
			$this->test();
		}
		if(!empty($_POST['created_by']))
			$user_id = $_POST['created_by'];
		else {
			$user = $this->google->get_user();
			$user_id = $user['id'];
		}
		$data = $this->model->get_data($user_id);
		$this->view->generate($this->view_file_name, 'template_view.php', $data);
	}

	function test(){
		echo 'test2';
		$google = $this->registry['google'];
		$service = new Google_Service_Calendar($google->client);

		$calendarId = 'primary';
		$optParams = array(
			'maxResults' => 10,
			'orderBy' => 'startTime',
			'singleEvents' => TRUE,
			'timeMin' => date('c'),
		);
		$results = $service->events->listEvents($calendarId, $optParams);

		if (count($results->getItems()) == 0) {
			echo "No upcoming events found.<br>";
		} else {
			echo "Upcoming events:<br>";
			foreach ($results->getItems() as $event) {
				$start = $event->start->dateTime;
				if (empty($start)) {
					$start = $event->start->date;
				}
				echo $event->getSummary() . '(' . $start . ')<br>';
			}
		}
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
			$google = $this->registry['google'];
			$data = $google->addCalendar($_GET['id']);
		}
		$this->view->generate($this->view_file_name, 'template_view.php', $data);
	}
	
}
?>
