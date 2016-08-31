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
		if(empty($_GET['id'])){
            $this->registry->set('error', 'Не вибрано жодного катендаря');
            $this->registry->set('info', 'Щоб додати/синхронізувати календар до/з Google, необхідно серед <a href="/calendars">всіх календарів</a> помітити галочкою один та натиснути на кнопку "Додати до Google Calendars"');
            $this->view->generate('error_view.php', 'template_view.php', $data);
            return;
        }
        $calendar = $this->model->get_calendar($_GET['id']);
        $data['calendar'] = $calendar;
        $google = $this->registry['google'];
        $service = new Google_Service_Calendar($google->client);
        $g_list = $service->calendarList->listCalendarList()->getItems();
        $exist = false;
        foreach($g_list as $g_item){
            if($g_item['id'] == $calendar['g_calendar_id'])
                $exist = true;
        }
        $data['task'] = array();
        if($calendar['g_calendar_id'] && $exist){
            // can sync
            $data['task'][] = 'sync';
            $data['task'][] = 'disconnect';
            foreach($g_list as $g_item) {
                if ($g_item['id'] == $calendar['g_calendar_id'])
                    $data['g_calendar_list_item'] = $g_item;
            }
            if(!empty($_GET['task'])) {
                if ($_GET['task'] == 'delete'){
                    $this->view->generate('calendars_add_to_google/delete_confirm.php', 'template_view.php', $data);
                } elseif ($_GET['task'] == 'delete_confirm'){
                    $g_calendar_id = $this->model->delete_g_calendar($calendar['id']);
                    if($g_calendar_id){
                        $result = $service->calendars->delete($g_calendar_id);
                        $data['result'] = $result;
                        $this->registry->set('info', 'Даний календар успішно видалений з Google');
                    } else {
                        $this->registry->set('error', 'Даний календар не зв\'язаний з Google. Неможливо видалити календар. g_calendar_id не знайдено');
                    }
                    $this->view->generate('calendars_add_to_google/delete_success.php', 'template_view.php', $data);
                } else {
                    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/calendars/add_to_google?id=' . $calendar['id'];
                    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
                }
            } else
                $this->view->generate($this->view_file_name, 'template_view.php', $data);
        } else {
            // add
            $data['task'][] = 'add';
            if(!empty($_GET['task']) && $_GET['task'] == 'add'){
                $g_calendar_name = ($data['calendar']['name'] == '') ? 'Немає імені' : $data['calendar']['name'];
                $data['g_calendar_name'] = $g_calendar_name;
                $g_calendar = new Google_Service_Calendar_Calendar();
                $g_calendar->setSummary($g_calendar_name);
                $timezone = (empty($calendar['timezone'])) ? 'Europe/Kiev' : $calendar['timezone'];
                $g_calendar->setTimeZone($timezone); // test
                $created_calendar = $service->calendars->insert($g_calendar);
                if(!empty($created_calendar['id'])) {
                    $result = $this->model->set_g_calendar($calendar['id'], $created_calendar['id']);
                    if ($result)
                        $this->registry->set('info', 'Ваш календар додано до Google');
                    else
                        $this->registry->set('error', 'Помилка під час додавання. Зв\'яжіться будь ласка з адміністраторами для з\'язування причини помилки');
                } else {
                    $this->registry->set('error', 'Помилка під час додавання до Google. Спробуйте вийти та авторизуватися заново');
                }
                $this->registry->set('debug', $created_calendar);
                $this->view->generate($this->view_file_name, 'template_view.php', $data);
            } else {
                $this->view->generate('calendars_add_to_google/add_confirm.php', 'template_view.php', $data);
            }
        }
        $data['calendar'] = $calendar;
	}
	
}
?>
