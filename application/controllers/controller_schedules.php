<?php
include "application/models/model_calendars.php";
include "application/models/model_user.php";

class Controller_Schedules extends Controller{

    function action_index(){
        $data = array();
        $model = new Model_Calendars($this->registry);
        $data['calendars'] = $model->get_public_calendars();
        $this->view->generate('schedules_index_view.php', 'template_view.php', $data);
    }

    function action_view($type = 'table'){
        if(empty($_GET['id'])){
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/schedules/index';
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
        $model_calendars = new Model_Calendars($this->registry);
        $data = $model_calendars->get_view_item($_GET['id'], true);
        $model_user = new Model_User($this->registry);
        $calendar_creator = $model_user->get_item($data['calendar']['created_by']);
        $data['calendar']['user_name'] = $calendar_creator['name'];
        $this->view->generate('schedules_' . $type. 'view_view.php', 'template_view.php', $data);
    }

	function action_block_view(){
		$this->action_view('block');
	}

	function action_table_view(){
		$this->action_view('table');
	}

}