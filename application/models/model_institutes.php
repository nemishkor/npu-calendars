<?php
class Model_Institutes extends Model
{
	
	function __construct($registry){
		$table_name = 'institutes';
		parent::__construct($registry, $table_name);
	}
		
	function get_edit_item($id = null){ // data for edit page
		$google = $this->registry['google'];
		$user = $google->get_user();
		if($id) {
			$query = "SELECT t.created_by,u.full_access FROM {$this->table_name} t JOIN users u ON t.created_by=u.id WHERE t.id={$id}";
			$result = $this->db->query($query);
			$result = $result->fetch_assoc();
			if($user['id'] == $result['created_by'] || stripos($result['full_access'], $user['email']) !== false){
				$item = $this->get_item($id);
				$data = array('item' => $item, 'access' => $result);
				return $data;
			} else {
				$this->registry->set('error', 'Access denied. You cannot view this content =/');
				return false;
			}
		} else {
			$item = array(
				'name'        => '',
				'created_by'  => $user['id'],
				'published'   => '1',
				'description' => '',
				'url'         => '',
			);
			$data = array('item' => $item);
			return $data;
		}
	}
	
	function create(){
		$item = $this->get_item_from_form();
		$query = "INSERT INTO `{$this->table_name}` VALUES(NULL, '{$item['published']}', '0', '{$item['created_by']}', '{$item['name']}', '{$item['description']}', '{$item['url']}')";
		$result = $this->db->query($query);
		if($result){
			return $this->db->insert_id;
		} else
			return false;
	}
	
	function save(){
		$item = $this->get_item_from_form();
		$query = "UPDATE {$this->table_name} SET name='{$item['name']}', published='{$item['published']}', description='{$item['description']}', url='{$item['url']} WHERE id='{$item['id']}'";
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
			$item = array('name'=>$_GET['name'], 'created_by'=>$user['id'], 'published'=>$_GET['published'], 'description'=>$_GET['description'], 'url'=>$_GET['url']);
			if(isset($_GET['id']) && $_GET['id'])
				$item['id'] = $_GET['id'];
		}
		return $item;
	}
	
}
?>
