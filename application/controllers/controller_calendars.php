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
	
	function action_schedules(){
		if(empty($_GET['id'])){
            $this->registry->set('error', 'Не вибрано жодного катендаря');
            $this->registry->set('info', 'Щоб додати/синхронізувати календар до/з Google, необхідно серед <a href="/calendars">всіх календарів</a> помітити галочкою один та натиснути на кнопку "Додати до Google Calendars"');
            $this->view->generate('error_view.php');
            return;
        }
        $data = $this->model->get_view_item($_GET['id']);
        $google = $this->registry['google'];
        $service = new Google_Service_Calendar($google->client);
        $g_list = $service->calendarList->listCalendarList()->getItems();
        $data['calendar']['g_calendars'] = $this->fix_broken_g_calendars($data['calendar'], $g_list);
        $data['g_calendar_list_items'] = $this->is_exists_in_google($data['calendar'], $g_list);
        $info_msg = array();
        if(!empty($_GET['task']) && $_GET['task'] == 'add'){
            if(!empty($_GET['groups'])) {
                $groups = $this->model->get_field(str_replace('-',',',$_GET['groups']), 'groups');
                foreach ($groups as $group):
                    $exist = false;
                    foreach ($data['calendar']['g_calendars'] as $key => $schedule) {
                        if ($key == 'group_' . $group['id'])
                            $exist = true;
                    }
                    if(!$exist) {
                        $new_calendar_name = 'Група ' . $group['name'];
                        $g_calendar = new Google_Service_Calendar_Calendar();
                        $g_calendar->setSummary($new_calendar_name);
                        $timezone = (empty($data['calendar']['timezone'])) ? 'Europe/Kiev' : $data['calendar']['timezone'];
                        $g_calendar->setTimeZone($timezone);
                        $created_calendar = $service->calendars->insert($g_calendar);
                        $data['created_calendar'] = $created_calendar;
                        $new_saved_schedule = 'group_' . $group['id'];
                        $data['calendar']['g_calendars']->$new_saved_schedule = $created_calendar['id'];
                        if (!empty($created_calendar['id'])) {
                            $result = $this->model->set_g_calendars($data['calendar']['id'], json_encode($data['calendar']['g_calendars']));
                            if ($result)
                                $info_msg[] = ' Групу ' . $group['name'] . ' додано до Google';
                            else
                                $this->registry->set('error', 'Помилка під час додавання. Зв\'яжіться будь ласка з адміністраторами для з\'язування причини помилки');
                        } else {
                            $this->registry->set('error', 'Помилка під час додавання до Google. Спробуйте вийти та авторизуватися заново');
                        }
                    } else {
                        $info_msg[] = ' Групу ' . $group['name'] . ' вже додана до Google!';
                    }
                endforeach;
            }
        }
        if(!empty($_GET['task']) && $_GET['task'] == 'delete'){
            if(!empty($_GET['groups'])) {
                $groups = $this->model->get_field(str_replace('-', ',', $_GET['groups']), 'groups');
                foreach ($groups as $group):
                    $g_calendar_id = null;
                    foreach ($data['g_calendar_list_items'] as $g_item)
                        foreach($data['calendar']['g_calendars'] as $key => $schedule)
                            if($schedule == $g_item['id'] && $key == 'group_' . $group['id'])
                                $g_calendar_id = $g_item['id'];
                    if(!is_null($g_calendar_id)){
                        $service->calendars->delete($g_calendar_id);
                    }
                endforeach;
            }
        }
        $g_list = $service->calendarList->listCalendarList()->getItems();
        $data['g_calendar_list_items'] = $g_list;
        $this->registry->set('info', implode('<br>', $info_msg));
        $this->view->generate($this->view_file_name, 'template_view.php', $data);
	}

    /**
     * @param array $calendar
     * @param Google_Service_Calendar_CalendarListEntry $g_list
     * @return array of Google_Service_Calendar_CalendarListEntry or false if it doesn't exists
     * @internal param array $calendar from database
     */
    function is_exists_in_google($calendar, $g_list = null){
        if(is_null($g_list)) {
            $google = $this->registry['google'];
            $service = new Google_Service_Calendar($google->client);
            $g_list = $service->calendarList->listCalendarList()->getItems();
        }
        $g_exists_list = array();
        foreach($g_list as $g_item){
            foreach ($calendar['g_calendars'] as $schedule)
                if($g_item['id'] == $schedule)
                    $g_exists_list[] = $g_item;
        }
        if(count($g_exists_list))
            return $g_exists_list;
        return false;
    }

    /**
     * @param array $calendar
     * @param Google_Service_Calendar_CalendarListEntry $g_list
     * @return bool true if db transaction is success
     * @internal param array $calendar from database
     * if calendar removed from calendar.google.com it sync google and our database
     */
    function fix_broken_g_calendars($calendar, $g_list = null){
        if(is_null($g_list)) {
            $google = $this->registry['google'];
            $service = new Google_Service_Calendar($google->client);
            $g_list = $service->calendarList->listCalendarList()->getItems();
        }
        $new_g_calendars = new stdClass();
        foreach($g_list as $g_item)
            foreach ($calendar['g_calendars'] as $key => $schedule)
                if($g_item['id'] == $schedule)
                    $new_g_calendars->$key = $schedule;
        $this->model->set_g_calendars($calendar['id'], json_encode($new_g_calendars));
        return $this->model->get_g_calendars($calendar['id']);
    }
	
}
?>
