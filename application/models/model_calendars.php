<?php
class Model_Calendars extends Model
{
	
	function __construct($registry){
		$table_name = 'calendars';
		parent::__construct($registry, $table_name);
	}

	public function get_timezones(){
		$timezones = array(
			'Europe/Amsterdam','Europe/Andorra','Europe/Astrakhan','Europe/Athens',
			'Europe/BelgradeEurope/Berlin','Europe/Bratislava','Europe/Brussels',
			'Europe/Bucharest','Europe/Budapest','Europe/Busingen','Europe/Chisinau',
			'Europe/Copenhagen','Europe/Dublin','Europe/Gibraltar','Europe/Guernsey',
			'Europe/Helsinki','Europe/Isle_of_Man','Europe/Istanbul','Europe/Jersey',
			'Europe/Kaliningrad','Europe/Kiev','Europe/Kirov','Europe/Lisbon',
			'Europe/Ljubljana','Europe/London','Europe/Luxembourg','Europe/Madrid',
			'Europe/Malta','Europe/Mariehamn','Europe/Minsk','Europe/Monaco',
			'Europe/Moscow','Europe/Oslo','Europe/Paris','Europe/Podgorica',
			'Europe/Prague','Europe/Riga','Europe/Rome','Europe/Samara',
			'Europe/San_Marino','Europe/Sarajevo','Europe/Simferopol','Europe/Skopje',
			'Europe/Sofia','Europe/Stockholm','Europe/Tallinn','Europe/Tirane',
			'Europe/Ulyanovsk','Europe/Uzhgorod','Europe/Vaduz','Europe/Vatican',
			'Europe/Vienna','Europe/Vilnius','Europe/Volgograd','Europe/Warsaw',
			'Europe/Zagreb','Europe/Zaporozhye','Europe/Zurich',
		);
		return $timezones;
	}
	
	function get_data($user_id, $user_email)
	{
		$data = array();
		$google = $this->registry['google'];
		$calendar_service = new Google_Service_Calendar($google->client);
		$data['user'] = $google->get_user();
		$data['user_calendars'] = parent::get_data($user_id);
		if($data['user'])
			$data['google_calendars'] = $calendar_service->calendarList->listCalendarList();
		if(!$data['user'] || $data['user']['id'] != $user_id) {
			$query = "SELECT name FROM users WHERE id={'$user_id'}";
			$result = $this->db->query($query);
			if($result) {
				$user_name = $result->fetch_assoc();
				$data['user_calendars']['user_name'] = $user_name['name'];
			}
		}
		if($user_id){
			$query = "SELECT t.*,u.full_access FROM {$this->table_name} t JOIN users u ON t.created_by=u.id";
			$query .= " WHERE u.full_access LIKE '%{$user_email}%'";
			$query .= " ORDER BY t.id DESC";
			$result = $this->db->query($query);
			$shared_items  = array();
			while ($row = $result->fetch_assoc()){
				$row['link'] = $this->registry['host'] . $this->registry['controller_name'] . '/edit?id=' . $row['id'];
				$shared_items[] = $row;
			}
			$data['shared_fields'] = $result->fetch_fields();
			$data['shared_items'] = $shared_items;
		}
		return $data;
	}

	function create(){
		$item = $this->get_item_from_form();
		$query = "INSERT INTO `{$this->table_name}` VALUES(NULL, '{$item['name']}', '{$item['published']}', '0', now(), '{$item['created_by']}', '{$item['events']}', '{$item['dual_week']}', NULL, '{$item['timezone']}', '{$item['start_date']}', '{$item['end_date']}')";
		$result = $this->db->query($query);
		if($result){
			return $this->db->insert_id;
		} else
			return false;
	}
	
	function save(){
		$item = $this->get_item_from_form();
		$query = "UPDATE {$this->table_name} SET name='{$item['name']}', published='{$item['published']}', events='{$item['events']}', dual_week='{$item['dual_week']}', timezone='{$item['timezone']}', start_date='{$item['start_date']}', end_date='{$item['end_date']}' WHERE id='{$item['id']}'";
		$result = $this->db->query($query);
		if($result){
			return $item['id'];
		} else
			return false;
	}
	
	function get_item_from_form(){
		$google = $this->registry['google'];
		$user = $google->get_user();
		$item = array(
		    'name'      => $_POST['name'],
            'created_by'=> $user['id'],
            'published' => $_POST['published'],
            'events'    => $_POST['events'],
            'dual_week' => ($_POST['dual_week'] == 'on') ? 1 : 0,
            'timezone'  => $_POST['timezone'],
            'start_date'=> $_POST['start_date'],
            'end_date'  => $_POST['end_date'],
        );
		if(isset($_POST['id']) && $_POST['id'])
			$item['id'] = $_POST['id'];
		return $item;
	}
	
	function get_view_item($id, $public = null){
		$google = $this->registry['google'];
		$data = array(
		    'calendar'=>$this->get_item($id, $public),
            'groups'=>array(),
            'courses'=>array(),
            'lectors'=>array(),
            'auditories'=>array(),
        );
		$groupsIds = array();
		$coursesIds = array();
		$lectorsIds = array();
		$auditoriesIds = array();
		if($data['calendar']){
			if($data['calendar']['name'] == '' || $data['calendar']['name'] == null)
				$data['calendar']['name'] = 'немає імені';
			$user = $google->get_user($data['calendar']['created_by']);
			if($user)
				$data['calendar']['created_by'] = $user['name'] . '(' . $user['email'] . ')';
			$events = $data['calendar']['events'];
			foreach($events as $week){
				foreach($week as $day){
					foreach($day as $lessons){
						foreach ($lessons as $lesson) {
							if ($lesson[0] == '' || $lesson[1] == '' || $lesson[2] == '' ||
								$lesson[0] == null || $lesson[1] == null || $lesson[2] == null)
								continue;
							if (!in_array($lesson[0], $data['groups']))
								$groupsIds[] = $lesson[0];
							if (!in_array($lesson[1], $data['courses']))
								$coursesIds[] = $lesson[1];
							if (!in_array($lesson[2], $data['lectors']))
								$lectorsIds[] = $lesson[2];
							if (!in_array($lesson[3], $data['auditories']))
								$auditoriesIds[] = $lesson[3];
						}
					}
				}
			}
			$data['groups'] = $this->get_groups(implode(',', $groupsIds));
			$data['courses'] = $this->get_courses(implode(',', $coursesIds));
			$data['lectors'] = $this->get_lectors(implode(',', $lectorsIds));
			$data['auditories'] = $this->get_auditories(implode(',', $auditoriesIds));
		}
		return $data;
	}

	function get_public_calendars(){
		$query = "SELECT c.id,c.name,c.created,c.start_date,c.end_date,u.name as user_name,u.organization FROM calendars AS c LEFT JOIN users AS u ON c.created_by=u.id WHERE c.published='1' AND c.trashed='0'";
		$result = $this->db->query($query);
		$items = array();
		while($row = $result->fetch_assoc()){
			if(is_null($row['user_name']))
				$row['user_name'] = 'Немає імені';
			$items[] = $row;
		}
		return $items;
	}
	
	function get_groups($ids){
		return $this->get_field($ids, 'groups');
	}
	
	function get_courses($ids){
		return $this->get_field($ids, 'courses');
	}
	
	function get_lectors($ids){
		return $this->get_field($ids, 'lectors');
	}
	
	function get_auditories($ids){
		return $this->get_field($ids, 'auditories');
	}
	
	function get_field($ids, $table_name = 'auditories'){
		$query = "SELECT * FROM {$table_name} WHERE published='1' AND trashed='0' AND id IN ({$ids})";
		$fields_result = $this->db->query($query);
		$fields = array();
		if($fields_result)
			while($row = $fields_result->fetch_assoc()){
				$fields[] = $row;
			}
		return $fields;
	}
	
	function get_edit_item($id = null){
		$google = $this->registry['google'];
		$user = $google->get_user();

		$query = "SELECT t.created_by,u.full_access FROM {$this->table_name} t JOIN users u ON t.created_by=u.id WHERE t.id={$id}";
		$result = $this->db->query($query);
		$result = $result->fetch_assoc();
		if($user['id'] == $result['created_by'] || stripos($result['full_access'], $user['email']) !== false){
			$access = $result;
			// get auditories
			$query             = "SELECT * FROM auditories WHERE published='1' AND trashed='0' AND created_by='{$user['id']}' ORDER BY name ASC";
			$auditories        = array();
			$auditories_result = $this->db->query($query);
			while ($row = $auditories_result->fetch_assoc()) {
				$auditories[] = $row;
			}
			$query = "SELECT a.* FROM auditories a JOIN users u ON a.created_by=u.id WHERE a.published='1' AND a.trashed='0' AND u.full_access LIKE '%{$user['email']}%' ORDER BY name ASC";
			$auditories_result = $this->db->query($query);
			while ($row = $auditories_result->fetch_assoc()) {
				$auditories[] = $row;
			}
			// get courses
			$query          = "SELECT * FROM courses WHERE published='1' AND trashed='0' AND created_by='{$user['id']}' ORDER BY name ASC";
			$courses        = array();
			$courses_result = $this->db->query($query);
			while ($row = $courses_result->fetch_assoc()) {
				$courses[] = $row;
			}
			$query = "SELECT a.* FROM courses a JOIN users u ON a.created_by=u.id WHERE a.published='1' AND a.trashed='0' AND u.full_access LIKE '%{$user['email']}%' ORDER BY name ASC";
			$courses_result = $this->db->query($query);
			while ($row = $courses_result->fetch_assoc()) {
				$courses[] = $row;
			}
			// get groups
			$query         = "SELECT * FROM groups WHERE published='1' AND trashed='0' AND created_by='{$user['id']}' ORDER BY name ASC";
			$groups        = array();
			$groups_result = $this->db->query($query);
			while ($row = $groups_result->fetch_assoc()) {
				$groups[] = $row;
			}
			$query = "SELECT a.* FROM groups a JOIN users u ON a.created_by=u.id WHERE a.published='1' AND a.trashed='0' AND u.full_access LIKE '%{$user['email']}%' ORDER BY name ASC";
			$groups_result = $this->db->query($query);
			while ($row = $groups_result->fetch_assoc()) {
				$groups[] = $row;
			}
			// get institutes
			$query             = "SELECT * FROM institutes WHERE published='1' AND trashed='0' AND created_by='{$user['id']}' ORDER BY name ASC";
			$institutes        = array();
			$institutes_result = $this->db->query($query);
			while ($row = $institutes_result->fetch_assoc()) {
				$institutes[] = $row;
			}
			$query = "SELECT a.* FROM institutes a JOIN users u ON a.created_by=u.id WHERE a.published='1' AND a.trashed='0' AND u.full_access LIKE '%{$user['email']}%' ORDER BY name ASC";
			$institutes_result = $this->db->query($query);
			while ($row = $institutes_result->fetch_assoc()) {
				$institutes[] = $row;
			}
			// get lectors
			$query          = "SELECT * FROM lectors WHERE published='1' AND trashed='0' AND created_by='{$user['id']}' ORDER BY name ASC";
			$lectors        = array();
			$lectors_result = $this->db->query($query);
			while ($row = $lectors_result->fetch_assoc()) {
				$lectors[] = $row;
			}
			$query = "SELECT a.* FROM lectors a JOIN users u ON a.created_by=u.id WHERE a.published='1' AND a.trashed='0' AND u.full_access LIKE '%{$user['email']}%' ORDER BY name ASC";
			$lectors_result = $this->db->query($query);
			while ($row = $lectors_result->fetch_assoc()) {
				$lectors[] = $row;
			}

			$data = array(
				'auditories' => $auditories,
				'courses'    => $courses,
				'groups'     => $groups,
				'institutes' => $institutes,
				'lectors'    => $lectors,
				'params'     => json_decode($user['params']),
				'timezones'  => $this->get_timezones(),
				'access'     => $access
			);
			if ($id == null) {
				$data['calendar'] = array(
					'name'       => '',
					'published'  => '1',
					'id'         => '',
					'timezone'   => null,
					'start_date' => date('Y') . '-09-01',
					'end_date'   => date('Y') . '-05-31',
				);
			} else {
				$data['calendar'] = $this->get_item($id);
			}

			return $data;
		} else {
			$this->registry->set('error', 'Access denied. You cannot view this content =/');
			return false;
		}

	}
	
	function get_item($id, $public = null){
		$query = "SELECT c.* FROM calendars c JOIN users u ON c.created_by=u.id WHERE c.id={$id}";
		if(is_null($public)) {
			$google = $this->registry['google'];
			$user = $google->get_user();
			$query .= " AND (c.created_by='{$user['id']}' OR u.full_access LIKE '%{$user['email']}%')";
		}
		$result = $this->db->query($query);
		if($result){
			$calendar = $result->fetch_assoc();

            $start_date = DateTime::createFromFormat('Y-m-d', $calendar['start_date']);
            if(!$start_date || !checkdate($start_date->format('m'), $start_date->format('d'), $start_date->format('Y')))
                $calendar['start_date'] = date('Y') . '-09-01';
            else
                $calendar['start_date'] = $start_date->format('Y-m-d');
            $end_date = DateTime::createFromFormat('Y-m-d', $calendar['end_date']);
            if(!$end_date || !checkdate($end_date->format('m'), $end_date->format('d'), $end_date->format('Y')))
                $calendar['end_date'] = (date('Y') + 1) . '-05-31';
            else
                $calendar['end_date'] = $end_date->format('Y-m-d');

			$calendar['events'] = json_decode($calendar['events']);
			if(is_null($calendar['g_calendars']) || $calendar['g_calendars'] == '')
			    $calendar['g_calendars'] = new stdClass();
			else
				$calendar['g_calendars'] = json_decode($calendar['g_calendars']);
			return $calendar;
		}
		return null;
	}

	function get_calendar($id){
		$query = "SELECT * FROM calendars WHERE id={$id}";
		$result = $this->db->query($query);
		if($result){
			$calendar = $result->fetch_assoc();
			$calendar['events'] = json_decode($calendar['events']);
			return $calendar;
		}
		return null;
	}

	function set_g_calendars($id, $g_id){
		$query = "UPDATE calendars SET g_calendars='{$g_id}' WHERE id='{$id}'";
		$result = $this->db->query($query);
		if($result)
			return true;
		else
			return false;
	}

    function get_g_calendars($id){
        $query = "SELECT g_calendars FROM calendars WHERE id='{$id}'";
        $result = $this->db->query($query);
        if($result) {
            $calendar = $result->fetch_assoc();
            if(is_null($calendar['g_calendars']) || $calendar['g_calendars'] == '')
                $calendar['g_calendars'] = new stdClass();
            else
                $calendar['g_calendars'] = json_decode($calendar['g_calendars']);
            return $calendar['g_calendars'];
        } else
            return false;
    }

    function delete_g_calendars($id){
        $g_calendars = $this->get_g_calendars($id);
        if ($g_calendars) {
            $query = "UPDATE calendars SET g_calendars=NULL WHERE id='{$id}'";
            $result = $this->db->query($query);
            if($result) {
                return $g_calendars;
            } else
                return false;
        } else
            return false;
    }

}
?>
