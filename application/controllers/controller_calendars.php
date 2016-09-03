<?php
include_once 'application/core/google/vendor/google/apiclient-services/Google/Service/Calendar/Event.php';
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
        if(!empty($_GET['task'])):
            $filters_base = array(
                'groups' => array(
                    'name of one item' => 'group',
                    'readable name' => 'Група',
                    'second readable name' => 'групи',
                ),
                'courses' => array(
                    'name of one item' => 'course',
                    'readable name' => 'Дисципліна',
                    'second readable name' => 'дисципліни',
                ),
                'lectors' => array(
                    'name of one item' => 'lector',
                    'readable name' => 'Викладач',
                    'second readable name' => 'викладача',
                ),
                'auditories' => array(
                    'name of one item' => 'auditory',
                    'readable name' => 'Аудиторія',
                    'second readable name' => 'аудиторії',
                ),
            );
            foreach ($filters_base as $filter_key => $filter_base) {
                if (empty($_GET[$filter_key]))
                    continue;
                $fields = $this->model->get_field(str_replace('-', ',', $_GET[$filter_key]), $filter_key);
                foreach ($fields as $field):
                    if($_GET['task'] == 'add'){
                        $exist = false;
                        foreach ($data['calendar']['g_calendars'] as $key => $schedule) {
                            if ($key == $filter_base['name of one item'] . '_' . $field['id'])
                                $exist = true;
                        }
                        if (!$exist) {
                            $new_calendar_name = $filter_base['readable name'] . ' ' . $field['name'];
                            $g_calendar = new Google_Service_Calendar_Calendar();
                            $g_calendar->setSummary($new_calendar_name);
                            $timezone = (empty($data['calendar']['timezone'])) ? 'Europe/Kiev' : $data['calendar']['timezone'];
                            $g_calendar->setTimeZone($timezone);
                            $created_calendar = $service->calendars->insert($g_calendar);
                            $data['created_calendar'] = $created_calendar;
                            $new_saved_schedule = $filter_base['name of one item'] . '_' . $field['id'];
                            $data['calendar']['g_calendars']->$new_saved_schedule = $created_calendar['id'];
                            if (!empty($created_calendar['id'])) {
                                $result = $this->model->set_g_calendars($data['calendar']['id'], json_encode($data['calendar']['g_calendars']));
                                if ($result) {
                                    $info_msg[] = 'Розклад ' . $filter_base['second readable name'] . ' ' . $field['name'] . ' [' . $field['id'] . '] доданий до Google';
                                    $this->add_schedule_to_g_calendar($data, $filter_base['name of one item'], $field['id'], $created_calendar['id'], $service);
                                } else
                                    $this->registry->set('error', 'Помилка під час додавання. Зв\'яжіться будь ласка з адміністраторами для з\'язування причини помилки');
                            } else
                                $this->registry->set('error', 'Помилка під час додавання до Google. Спробуйте вийти та авторизуватися заново');
                        } else
                            $info_msg[] = 'Розклад ' . $filter_base['second readable name'] . ' ' . $field['name'] . ' [' . $field['id'] . '] вже доданий до Google!';
                    }
                    if($_GET['task'] == 'delete') {
                        $fields = $this->model->get_field(str_replace('-', ',', $_GET[$filter_key]), $filter_key);
                        foreach ($fields as $field):
                            $g_calendar_id = null;
                            if ($data['g_calendar_list_items'])
                                foreach ($data['g_calendar_list_items'] as $g_item)
                                    foreach ($data['calendar']['g_calendars'] as $key => $schedule)
                                        if ($schedule == $g_item['id'] && $key == $filter_base['name of one item'] . '_' . $field['id'])
                                            $g_calendar_id = $g_item['id'];
                            if (!is_null($g_calendar_id)) {
                                $result = $service->calendars->delete($g_calendar_id);
                                if ($result)
                                    $info_msg[] = 'Розклад ' . $filter_base['second readable name'] . ' ' . $field['name'] . ' [' . $field['id'] . '] видалений з Google';
                                else
                                    $this->registry->set('Виникла помилка під час видалення ' . $filter_base['second readable name'] . ' ' . $field['name'] . ' [' . $field['id'] . '] з Google');
                            } else
                                $info_msg[] = 'Розклад ' . $filter_base['second readable name'] . ' ' . $field['name'] . ' [' . $field['id'] . '] вже видалений з Google. Якщо це не так, ви можете видалити його з <a href="http://calendar.google.com">calendar.google.com</a>';
                        endforeach;
                    }
                endforeach;
            }
        endif;
        // update google data after editing for view
        if(!empty($_GET['task'])) {
            $g_list = $service->calendarList->listCalendarList()->getItems();
            $data['calendar']['g_calendars'] = $this->fix_broken_g_calendars($data['calendar'], $g_list);
            $data['g_calendar_list_items'] = $this->is_exists_in_google($data['calendar'], $g_list);
        }
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
        return $new_g_calendars;
    }

    /**
     * @param array $data from model->get_view_item();
     * @param string $filter_name = ['group' || 'course' || 'lector' || 'auditory']
     * @param int $filter_id
     * @param string $g_calendar_id
     * @param Google_Service_Calendar $service
     * @return bool result
     */
    function add_schedule_to_g_calendar($data, $filter_name, $filter_id, $g_calendar_id, $service = null){
        if(is_null($service)) {
            $google = $this->registry['google'];
            $service = new Google_Service_Calendar($google->client);
        }
        $field = array('group' => 0, 'course' => 1, 'lector' => 2, 'auditory' => 3);
        $timezone = (empty($data['calendar']['timezone'])) ? 'Europe/Kiev' : $data['calendar']['timezone'];

        foreach ($data['calendar']['events'] as $week_key => $week){
            foreach ($week as $day_key => $day){
                if($data['calendar']['dual_week'] == '0' && $week_key == 1)
                    continue;
                foreach ($day as $lesson_key => $lesson) {
                    if ($lesson[$field[$filter_name]] != $filter_id)
                        continue;
                    if (is_null($lesson[0]) || is_null($lesson[1]) || is_null($lesson[2]) || is_null($lesson[3]))
                        continue;
                    $group = $lesson[0];
                    $course = $lesson[1];
                    $lector = $lesson[2];
                    $auditory = $lesson[3];
                    foreach ($data['groups'] as $g)
                        if ($group == $g['id'])
                            $group = $g;
                    foreach ($data['courses'] as $c)
                        if ($course == $c['id'])
                            $course = $c;
                    foreach ($data['lectors'] as $l)
                        if ($lector == $l['id'])
                            $lector = $l;
                    foreach ($data['auditories'] as $a)
                        if ($auditory == $a['id'])
                            $auditory = $a;
                    if ($filter_name == 'group')
                        $summary = $course['name'] . ' - ' . $lector['name'];
                    elseif ($filter_name == 'course')
                        $summary = $group['name'] . ' - ' . $lector['name'];
                    elseif ($filter_name == 'lector')
                        $summary = $group['name'] . ' - ' . $course['name'];
                    elseif ($filter_name == 'auditory')
                        $summary = $group['name'] . ' - ' . $lector['name'];
                    else
                        $summary = '$filter_name invalid value';
                    $start = (is_null($data['calendar']['start_date'])) ? date('Y') . '-09-01' : $data['calendar']['start_date'];
                    $end = $start;
                    $until = (is_null($data['calendar']['end_date'])) ? (date('Y') + 1) . '-05-31' : $data['calendar']['end_date'];
                    $start .= 'T';
                    $end .= 'T';
                    switch ($lesson_key) {
                        case 0:
                            $start .= '08:00:00';
                            $end .= '09:20:00';
                            break;
                        case 1:
                            $start .= '09:30:00';
                            $end .= '10:50:00';
                            break;
                        case 2:
                            $start .= '11:00:00';
                            $end .= '12:20:00';
                            break;
                        case 3:
                            $start = '12:30:00';
                            $end = '13:50:00';
                            break;
                        case 4:
                            $start .= '14:00:00';
                            $end .= '15:50:00';
                            break;
                        case 5:
                            $start .= '16:00:00';
                            $end .= '17:20:00';
                            break;
                        case 6:
                            $start .= '17:30:00';
                            $end .= '18:50:00';
                            break;
                        case 7:
                            $start .= '19:00:00';
                            $end .= '20:20:00';
                            break;
                        case 8:
                            $start .= '20:30:00';
                            $end .= '21:50:00';
                            break;
                        default:
                            $start .= '00:00:00';
                            $end .= '00:00:00';
                            break;
                    }
                    $recurrence = 'RRULE:FREQ=WEEKLY;';
                    if ($data['calendar']['dual_week'] == "1")
                        $recurrence .= 'INTERVAL=2;';
                    $recurrence .= 'UNTIL=' . str_replace('-', '', $until) . 'T235959Z;';
                    $recurrence .= 'WKST=MO;'; // day on which the workweek starts
                    $byday = array('MO','TU','WE','TH','FR','SA');
                    $recurrence .= 'BYDAY=' . $byday[$day_key] . ';';
                    $event = new Google_Service_Calendar_Event(array(
                        'summary' => $summary,
                        'location' => $auditory['name'],
                        'description' => $data['calendar']['name'],
                        'start' => array(
                            'dateTime' => $start,
                            'timeZone' => $timezone,
                        ),
                        'end' => array(
                            'dateTime' => $end,
                            'timeZone' => $timezone,
                        ),
                        'recurrence' => array(
                            $recurrence
                        ),
                        'reminders' => array(
                            'useDefault' => FALSE,
                            'overrides' => array(
                                array('method' => 'popup', 'minutes' => 10),
                            ),
                        ),
                    ));
                    $service->events->insert($g_calendar_id, $event);
                }
            }
        }

    }
	
}
?>
