<?php
class Model_Groups extends Model
{
	
	function __construct($registry){
		$table_name = 'groups';
		parent::__construct($registry, $table_name);
	}
	
	function get_data($user_id, $user_email){ // override default data for index page
		$query = "SELECT * FROM {$this->table_name}";
		if($user_id)
			$query .= " WHERE created_by='{$user_id}'";
		$query .= " ORDER BY id DESC";
		$result = $this->db->query($query);
		$items = array();
		while($row = $result->fetch_assoc()){
			$query = "SELECT id,name,created_by FROM institutes WHERE id='{$row['institute']}'";
			$institute_result = $this->db->query($query);
			$row['institute'] = $institute_result->fetch_assoc();
			if($row['institute']['created_by'] == $user_id)
				$row['institute']['link'] = $this->registry['host'] . 'institutes/edit?id=' . $row['institute']['id'];
			if($row['created_by'] == $user_id)
				$row['link'] = $this->registry['host'] . $this->registry['controller_name'] .'/edit?id=' . $row['id'];
			$items[] = $row;
		}
		$data = array('fields'=>$result->fetch_fields(), 'items'=>$items);
		if($user_id){
			$query = "SELECT t.*,u.full_access FROM {$this->table_name} t JOIN users u ON t.created_by=u.id";
			$query .= " WHERE u.full_access LIKE '%{$user_email}%'";
			$query .= " ORDER BY t.id DESC";
			$result = $this->db->query($query);
			$shared_items  = array();
			while ($row = $result->fetch_assoc()){
				$query = "SELECT i.id,i.name,i.created_by,u.full_access 
						  FROM institutes i JOIN users u ON i.created_by=u.id 
						  WHERE i.id='{$row['institute']}'";
				$institute_result = $this->db->query($query);
				$row['institute'] = $institute_result->fetch_assoc();
				if($row['institute']['created_by'] == $user_id || strpos($row['institute']['full_access'], $user_email) !== false)
					$row['institute']['link'] = $this->registry['host'] . 'institutes/edit?id=' . $row['institute']['id'];

				$row['link'] = $this->registry['host'] . $this->registry['controller_name'] . '/edit?id=' . $row['id'];
				$shared_items[] = $row;
			}
			$data['shared_fields'] = $result->fetch_fields();
			$data['shared_items'] = $shared_items;
		}
		return $data;
	}
		
	function get_edit_item($id = null){ // data for edit page
		if($id)
			$item = $this->get_item($id);
		else
			$item = array( 
				'published'=>'1',
				'created_by'=>$user['id'],
				'name'=>'', 
				'institute'=>'-1',
				'url'=>'', 
				);
		$data = array('item'=>$item, 'institutes'=>$this->get_institutes());
		return $data;
	}
	
	function create(){
		$item = $this->get_item_from_form();
		$query = "INSERT INTO `{$this->table_name}` VALUES(NULL, '{$item['published']}', '0', '{$item['created_by']}', '{$item['name']}', '{$item['institute']}', '{$item['url']}')";
		$result = $this->db->query($query);
		if($result){
			return $this->db->insert_id;
		} else
			return false;
	}
	
	function save(){
		$item = $this->get_item_from_form();
		$query = "UPDATE {$this->table_name} SET name='{$item['name']}', published='{$item['published']}', institute='{$item['institute']}', url='{$item['url']}' WHERE id='{$item['id']}'";
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
			$item = array('name'=>$_GET['name'], 'created_by'=>$user['id'], 'institute'=>$_GET['institute'], 'published'=>$_GET['published'], 'url'=>$_GET['url']);
			if(isset($_GET['id']) && $_GET['id'])
				$item['id'] = $_GET['id'];
		}
		return $item;
	}
	
	function get_institutes(){
		$query = "SELECT * FROM institutes WHERE published='1' AND trashed='0'";
		$result = $this->db->query($query);
		if($result){
			$institutes = array();
			while($institute = $result->fetch_assoc()){
				$institutes[] = $institute;
			}
			return $institutes;
		} else {
			return false;
		}
	}
	
}
?>
