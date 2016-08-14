<?php
class Model_Courses extends Model
{
	
	function __construct($registry){
		$table_name = 'courses';
		parent::__construct($registry, $table_name);
	}
		
	function get_edit_item($id = null){ // data for edit page
		if($id)
			$item = $this->get_item($id);
		else
			$item = array( 
				'published'=>'1',
				'created_by'=>$user['id'],
				'name'=>'', 
				'description'=>'', 
				);
		$data = array('item'=>$item);
		return $data;
	}
	
	function create(){
		$item = $this->get_item_from_form();
		$query = "INSERT INTO `{$this->table_name}` VALUES(NULL, '{$item['published']}', '0', '{$item['created_by']}', '{$item['name']}', '{$item['description']}')";
		$result = $this->db->query($query);
		if($result){
			return $this->db->insert_id;
		} else
			return false;
	}
	
	function save(){
		$item = $this->get_item_from_form();
		$query = "UPDATE {$this->table_name} SET name='{$item['name']}', published='{$item['published']}', description='{$item['description']}' WHERE id='{$item['id']}'";
		$result = $this->db->query($query);
		if($result){
			return $item['id'];
		} else
			return false;
	}
	
	function get_item_from_form(){
		$google = $this->registry['google'];
		$user = $google->get_user();
		if(isset($_GET['name']) && $_GET['name']){
			$item = array('name'=>$_GET['name'], 'created_by'=>$user['id'], 'published'=>$_GET['published'], 'description'=>$_GET['description']);
			if(isset($_GET['id']) && $_GET['id'])
				$item['id'] = $_GET['id'];
		}
		return $item;
	}
	
}
?>
