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
	    // swap group, course, lector, aud ids to arrays
	    foreach ($data['calendar']['events'] as $week_key => $week){
	    	foreach ($week as $day_key => $day){
	    		foreach ($day as $ex_key => $ex){
	    			foreach ($ex as $lesson_key => $lesson){
	    				// get group
	    				foreach ($data['groups'] as $group){
	    					if($group['id'] == $lesson[0]){
	    					    $data['calendar']['events'][$week_key][$day_key][$ex_key][$lesson_key][0] = $group;
							    break;
						    }
					    }
					    // get course
					    foreach ($data['courses'] as $course){
						    if($course['id'] == $lesson[1]){
							    $data['calendar']['events'][$week_key][$day_key][$ex_key][$lesson_key][1] = $course;
							    break;
						    }
					    }
					    // get lectors
					    foreach ($data['lectors'] as $lector){
						    if($lector['id'] == $lesson[2]){
							    $data['calendar']['events'][$week_key][$day_key][$ex_key][$lesson_key][2] = $lector;
							    break;
						    }
					    }
					    // get auditory
					    foreach ($data['auditories'] as $auditory){
						    if($auditory['id'] == $lesson[3]){
							    $data['calendar']['events'][$week_key][$day_key][$ex_key][$lesson_key][3] = $auditory;
							    break;
						    }
					    }

				    } // lesson
			    } // ex
		    } // day
	    } // week
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