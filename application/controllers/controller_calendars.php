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
            $google = $this->registry['google'];
            $service = new Google_Service_Calendar($google->client);
            $g_list = $service->calendarList->listCalendarList()->getItems();
            $exist = false;
            foreach($g_list as $g_item){
                if($g_item['id'] == $calendar['g_calendar_id'])
                    $exist = true;
            }
            $data['task'] = array();
            if($data['calendar']['g_calendar_id'] && $exist){
                // can sync
                $data['task'][] = 'sync';
                $data['task'][] = 'disconnect';
                foreach($g_list as $g_item) {
                    if ($g_item['id'] == $calendar['g_calendar_id'])
                        $data['g_calendar_list_item'] = $g_item;
                }
            } else {
                // add
                $data['task'][] = 'add';
                if(empty($_GET['task'])){

                } else {
                    $g_calendar_name = ($data['calendar']['name'] == '') ? 'Немає імені' : $data['calendar']['name'];
                    $data['g_calendar_name'] = $g_calendar_name;
                    $g_calendar = new Google_Service_Calendar_Calendar();
                    $g_calendar->setSummary($g_calendar_name);
                    $g_calendar->setTimeZone('Europe/Kiev');
                    $created_calendar = $service->calendars->insert($g_calendar);
                    if(!empty($created_calendar['id'])) {
                        $result = $this->model->set_g_calendar($data['calendar']['id'], $created_calendar['id']);
                        if ($result)
                            $this->registry->set('info', 'Ваш календар додано до Google');
                        else
                            $this->registry->set('error', 'Помилка під час додавання. Зв\'яжіться будь ласка з адміністраторами для з\'язування причини помилки');
                    } else {
                        $this->registry->set('error', 'Помилка під час додавання до Google. Спробуйте вийти та авторизуватися заново');
                    }
                    $this->registry->set('debug', $created_calendar);
                }
            }
            $data['calendar'] = $calendar;
		} else {
			$this->registry->set('error', 'Не вибрано жодного катендаря');
			$this->registry->set('info', 'Щоб додати/синхронізувати календар до/з Google, необхідно серед <a href="/calendars">всіх календарів</a> помітити галочкою один та натиснути на кнопку "Додати до Google Calendars"');
		}
		$this->view->generate($this->view_file_name, 'template_view.php', $data);
	}
	
}
?>
