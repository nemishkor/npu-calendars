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
		$query = "SELECT u.id,u.email,u.name,u.params,g.id AS group_id,g.name AS group_name FROM {$this->table_name} AS u LEFT JOIN users_groups AS g ON u.group_id=g.id WHERE u.id={$user['id']}";
		$result = $this->db->query($query);
		$row = $result->fetch_assoc();
		if($this->registry['action_name'] == 'edit') {
			$row["params"] = json_decode($row['params']);
		}
		$model_calendars = new Model_Calendars($this->registry);
		$data['timezones'] = $model_calendars->get_timezones();
		$data['user'] = $row;
		return $data;
	}
	
	function save(){
		$item = $this->get_item_from_form();
		$query = "UPDATE {$this->table_name} SET name='{$item['name']}',params='{$item['params']}' WHERE id='{$item['id']}'";
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
		$params = array(
			"dual_week"	=> $_POST['dual_week'],
			'timezone'	=> $_POST['timezone']
		);
		$item = array('id'=>$user['id'], 'name'=>$_POST['name'], 'params'=>json_encode($params));
		return $item;
	}

	function register_user($google_id, $email, $hash){
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
