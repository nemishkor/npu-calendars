<?php
class Model
{
	protected $db;
	protected $registry;
	protected $table_name;
	
	function __construct($registry, $table_name = ''){
		$this->registry = $registry;
		if(empty($this->registry['model'])) {
			$this->registry->set('model', $this);
		}
		$this->table_name = $table_name;
		if(empty($this->registry['db'])) {
			$this->db = new mysqli("localhost", "nemis206_tmp", "6LW{[!h_zJ?D", "nemis206_tmp");
			if ($this->db->connect_error) {
				die('Ошибка подключения (' . $this->db->connect_errno . ') '
					. $this->db->connect_error);
			}
			if (mysqli_connect_error()) {
				die('Ошибка подключения (' . mysqli_connect_errno() . ') '
					. mysqli_connect_error());
			}
			mysqli_set_charset($this->db, 'utf-8');
			mysqli_query($this->db, "SET NAMES utf8");
			mysqli_query($this->db, "Set session time_zone = '+2:00'");
			$this->registry->set('db', $this->db);
		} else {
			$this->db = $this->registry['db'];
		}
	}
	
	function get_table_name(){
		if(!empty($this->table_name))
			return $this->table_name;
		else
			return false;
	}

	function get_data($user_id){ // default data for index page
		$query = "SELECT * FROM {$this->table_name}";
		if($user_id)
			$query .= " WHERE created_by='{$user_id}'";
		$query .= " ORDER BY id DESC";
		$result = $this->db->query($query);
		$items = array();
		while($row = $result->fetch_assoc()){
			if($row['created_by'] == $user_id)
				$row['link'] = $this->registry['host'] . $this->registry['controller_name'] .'/edit?id=' . $row['id'];
			$items[] = $row;
		}
		$data = array('fields'=>$result->fetch_fields(), 'items'=>$items);
		return $data;
	}
	
	function get_edit_item($id){ // default data for edit page
		$data = array('item'=>$this->get_item($id));
	}
	
	function get_items($ids){
		$query = "SELECT * FROM {$this->table_name} WHERE id IN ({$ids})";
		$result = $this->db->query($query);
		return $result;
	}

	function get_item($id){
		$result = $this->db->query("SELECT * FROM {$this->table_name} WHERE id='{$id}'");
		if($result){
			$item = $result->fetch_assoc();
			return $item;
		} else
			return false;
	}

	function get_institute($id){
		$result = $this->db->query("SELECT * FROM institutes WHERE id='{$id}'");
		if($result){
			$item = $result->fetch_assoc();
			return $item;
		} else
			return false;
	}
	
	function save($item = null){
	}
	
	function trash($ids, $trash = 1){
		$query = "UPDATE {$this->table_name} SET trashed='{$trash}' WHERE id IN ({$ids})";
		$result = $this->db->query($query);
		return $result;
	}
	
	function delete($ids){
		$query = "DELETE FROM {$this->table_name} WHERE id IN ({$ids})";
		$result = $this->db->query($query);
		return $result;
	}
	
}
?>
