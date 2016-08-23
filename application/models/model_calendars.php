<?php
class Model_Calendars extends Model
{
	
	function __construct($registry){
		$table_name = 'calendars';
		parent::__construct($registry, $table_name);
	}	
	
	function create(){
		$item = $this->get_item_from_form();
		$query = "INSERT INTO `{$this->table_name}` VALUES(NULL, '{$item['name']}', '{$item['published']}', '0', now(), '{$item['created_by']}', '{$item['events']}')";
		//~ echo $query . '<br>';
		$result = $this->db->query($query);
		if($result){
			return $this->db->insert_id;
		} else
			return false;
	}
	
	function save(){
		$item = $this->get_item_from_form();
		$query = "UPDATE {$this->table_name} SET name='{$item['name']}', published='{$item['published']}', events='{$item['events']}', dual_week='{$item['dual_week']}' WHERE id='{$item['id']}'";
		//~ echo $query . '<br>';
		$result = $this->db->query($query);
		if($result){
			return $item['id'];
		} else
			return false;
	}
	
	function get_item_from_form(){
		$google = $this->registry['google'];
		$user = $google->get_user();
		$dual_week = ($_POST['dual_week']) ? '1' : '0';
		$item = array('name'=>$_POST['name'], 'created_by'=>$user['id'], 'published'=>$_POST['published'], 'events'=>$_POST['events'], 'dual_week'=>$dual_week);
		if(isset($_POST['id']) && $_POST['id'])
			$item['id'] = $_POST['id'];
		return $item;
	}
	
	function get_view_item($id){
		$google = $this->registry['google'];
		$data = array('calendar'=>$this->get_item($id), 'groups'=>array(), 'courses'=>array(), 'lectors'=>array(), 'auditories'=>array(),);
		$groupsIds = array();
		$coursesIds = array();
		$lectorsIds = array();
		$auditoriesIds = array();
		if($data['calendar']){
			if($data['calendar']['name'] == '' || $data['calendar']['name'] == null)
				$data['calendar']['name'] = 'немає імені';
			$user = $google->get_user($data['calendar']['created_by']);
			$data['calendar']['created_by'] = $user['name'] . '(' . $user['email'] . ')';
			$events = $data['calendar']['events'];
			foreach($events as $week){
				foreach($week as $day){
					foreach($day as $lesson){
						if($lesson[0] != '' && $lesson[1] != '' && $lesson[2] != '' &&
						$lesson[0] != null && $lesson[1] != null && $lesson[2] != null){
							if(!in_array($lesson[0], $data['groups']))
								$groupsIds[] = $lesson[0];
							if(!in_array($lesson[1], $data['courses']))
								$coursesIds[] = $lesson[1];
							if(!in_array($lesson[2], $data['lectors']))
								$lectorsIds[] = $lesson[2];
							if(!in_array($lesson[3], $data['auditories']))
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
		// get auditories
		$query = "SELECT * FROM auditories WHERE published='1' AND trashed='0' AND created_by='{$user[id]}'";
		$auditories_result = $this->db->query($query);
		$auditories = array();
		while($row = $auditories_result->fetch_assoc()){
			$auditories[] = $row;
		}
		// get courses
		$query = "SELECT * FROM courses WHERE published='1' AND trashed='0' AND created_by='{$user[id]}'";
		$courses_result = $this->db->query($query);
		$courses = array();
		while($row = $courses_result->fetch_assoc()){
			$courses[] = $row;
		}
		// get groups
		$query = "SELECT * FROM groups WHERE published='1' AND trashed='0' AND created_by='{$user[id]}'";
		$groups_result = $this->db->query($query);
		$groups = array();
		while($row = $groups_result->fetch_assoc()){
			$groups[] = $row;
		}
		// get institutes
		$query = "SELECT * FROM institutes WHERE published='1' AND trashed='0' AND created_by='{$user[id]}'";
		$institutes_result = $this->db->query($query);
		$institutes = array();
		while($row = $institutes_result->fetch_assoc()){
			$institutes[] = $row;
		}
		// get lectors
		$query = "SELECT * FROM lectors WHERE published='1' AND trashed='0' AND created_by='{$user[id]}'";
		$lectors_result = $this->db->query($query);
		$lectors = array();
		while($row = $lectors_result->fetch_assoc()){
			$lectors[] = $row;
		}
		
		$data = array(
			'auditories'=>$auditories,
			'courses'=>$courses,
			'groups'=>$groups,
			'institutes'=>$institutes,
			'lectors'=>$lectors,
			'params'=>json_decode($user['params']),
		);
		if($id == null){
			$data['calendar'] = array(name=>'', 'published'=>'1', 'id'=>'');
		} else {
			$data['calendar'] = $this->get_item($id);
		}
		return $data;
	}
	
	function get_item($id){
		$google = $this->registry['google'];
		$user = $google->get_user();
		$query = "SELECT * FROM calendars WHERE id={$id} AND created_by='{$user[id]}'";
		$result = $this->db->query($query);
		if($result){
			$calendar = $result->fetch_assoc();
			$calendar['events'] = json_decode($calendar['events']);
			return $calendar;
		}
		return null;
	}
	
}
?>