<?php
include_once "model_calendars.php";

class Model_User extends Model{
	function __construct($registry){
		$table_name = 'users';
		parent::__construct($registry, $table_name);
	}
	 
	function get_data(){
		$data = array();
		$google = $this->registry['google'];
		$user = $google->get_user();
		$query = "SELECT u.id,u.email,u.name,u.params,u.full_access,u.organization,g.id AS group_id,g.name AS group_name FROM {$this->table_name} AS u LEFT JOIN users_groups AS g ON u.group_id=g.id WHERE u.id={$user['id']}";
		$result = $this->db->query($query);
		$row = $result->fetch_assoc();
		if($this->registry['action_name'] == 'edit') {
			$row["params"] = json_decode($row['params']);
			if($row['full_access'] == null)
				$row['full_access'] = array();
			$row["full_access"] = json_decode($row['full_access']);
		}
		$model_calendars = new Model_Calendars($this->registry);
		$data['timezones'] = $model_calendars->get_timezones();
		$data['user'] = $row;
		return $data;
	}
	
	function save(){
		$item = $this->get_item_from_form();
		$query = "UPDATE {$this->table_name} SET name='{$item['name']}',params='{$item['params']}',full_access='{$item['full_access']}',organization='{$item['organization']}' WHERE id='{$item['id']}'";
		$result = $this->db->query($query);
		if($result){
			return $item['id'];
		} else
			return false;
	}

	function get_user(){
		if(empty($_SESSION['access_token']))
		    return false;
        $access_token = $_SESSION['access_token'];
        $query = "SELECT * FROM {$this->table_name} WHERE hash='{$access_token['access_token']}'";
        $result = $this->db->query($query);
        $user = $result->fetch_assoc();
        return $user;
	}

	function get_user_group($group_id = null){
		if(is_null($group_id))
		    return false;
        $query = "SELECT * FROM users_groups WHERE id={$group_id}";
        $result = $this->db->query($query);
        $group = $result->fetch_assoc();
        return $group;
	}
	
	function get_item_from_form(){
		$google = $this->registry['google'];
		$user = $google->get_user();
		$start_date = DateTime::createFromFormat('Y-m-d', $_POST['start_date']);
		if(!$start_date || !checkdate($start_date->format('m'), $start_date->format('d'), $start_date->format('Y')))
			$start_date = date('Y') . '-09-01';
		else
			$start_date = $start_date->format('Y-m-d');
		$end_date = DateTime::createFromFormat('Y-m-d', $_POST['end_date']);
		if(!$end_date || !checkdate($end_date->format('m'), $end_date->format('d'), $end_date->format('Y')))
			$end_date = (date('Y') + 1) . '-05-31';
		else
			$end_date = $end_date->format('Y-m-d');
		$params = array(
			'dual_week'	  => $_POST['dual_week'],
			'timezone'	  => $_POST['timezone'],
			'start_date'  => $start_date,
			'end_date'    => $end_date
		);
		$full_access = !empty($_POST['full_access']) ? $_POST['full_access'] : array();
		foreach ($full_access as $key => $email){
			if($email == "")
				unset($full_access[$key]);
		}
		$item = array(
			'id'	        => $user['id'],
			'name'	        => $_POST['name'],
			'organization'  => $_POST['organization'],
			'params'        => json_encode($params),
			'full_access'   => json_encode($full_access)
		);
		return $item;
	}

	function register_user($google_id, $email, $hash){
		$params = new stdClass();
		$params->timezone = "Europe/Kiev";
		$params->start_date = date('Y') . '-09-01';
		$params->end_date = (date('Y') + 1) . '-05-31';
		$this->db->query("INSERT INTO {$this->table_name} (google_id, email, hash) VALUES ('{$google_id}', '{$email}', '{$hash}')");
	}

	function update_hash($email, $hash){
		$this->db->query("UPDATE {$this->table_name} SET hash='{$hash}' WHERE email='{$email}'");
	}

	function check_user_exist($email){
		$result = $this->db->query("SELECT * FROM {$this->table_name} WHERE email='{$email}'");
		if($result->num_rows)
			return true;
		else
			return false;
	}
		
}
?>
