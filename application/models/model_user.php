<?php
class Model_User extends Model{
	function __construct($registry){
		$table_name = 'users';
		parent::__construct($registry, $table_name);
	}
	 
	function get_data(){
		$google = $this->registry['google'];
		$user = $google->get_user();
		$query = "SELECT u.id,u.email,u.name,u.params,g.id AS group_id,g.name AS group_name FROM {$this->table_name} AS u LEFT JOIN users_groups AS g ON u.group_id=g.id WHERE u.id={$user['id']}";
		$result = $this->db->query($query);
		$row = $result->fetch_assoc();
		if($this->registry['action'] == edit)
			$row["params"] = json_decode($row['params']);
		$data = array('user'=>$row);
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
	
	function get_item_from_form(){
		$google = $this->registry['google'];
		$user = $google->get_user();
		$params = array("dual_week"=>$_POST['dual_week']);
		$item = array('id'=>$user['id'], 'name'=>$_POST['name'], 'params'=>json_encode($params));
		return $item;
	}
		
}
?>
