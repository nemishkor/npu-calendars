<?php
class Model_Lectors extends Model
{
	
	function __construct($registry){
		$table_name = 'lectors';
		parent::__construct($registry, $table_name);
	}

	function new_item($item = null){
		$new_item = array(
			'published'=>'1',
			'name'=>'',
			'surname'=>'',
			'lastname'=>'',
			'gender'=>'m',
			'description'=>'',
			'url'=>'',
			'institute'=>'-1',
		);
		if(!is_null($item))
			foreach($item as $item_key => $item_value)
				foreach ($new_item as $new_item_key => $new_item_value)
					if($item_key == $new_item_key)
						$new_item[$new_item_key] = $item_value;
		$google = $this->registry['google'];
		$user = $google->get_user();
		if(empty($user['id']))
			$new_item['created_by'] = null;
		else
			$new_item['created_by'] = $user['id'];
		return $new_item;
	}
	
	function get_data($user_id, $user_email){ // default data for index page
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
	
	function create(){
		$item = $this->get_item_from_form();
		$query = "INSERT INTO `{$this->table_name}` VALUES(NULL, '{$item['published']}', '0', '{$item['created_by']}', '{$item['name']}', '{$item['surname']}', '{$item['lastname']}', '{$item['gender']}', '{$item['description']}', '{$item['url']}', '{$item['institute']}')";
		$result = $this->db->query($query);
		if($result){
			return $this->db->insert_id;
		} else
			return false;
	}
		
	function save(){
		$item = $this->get_item_from_form();
		$query = "UPDATE {$this->table_name} SET published='{$item['published']}', name='{$item['name']}', surname='{$item['surname']}', lastname='{$item['lastname']}', gender='{$item['gender']}', description='{$item['description']}', url='{$item['url']}', institute='{$item['institute']}' WHERE id='{$item['id']}'";
		$result = $this->db->query($query);
		if($result){
			return $item['id'];
		} else
			return false;
	}

	function save_obj($item){
		$item = $this->new_item($item);
		$query = "INSERT INTO `{$this->table_name}` VALUES(NULL, '{$item['published']}', '0', '{$item['created_by']}', '{$item['name']}', '{$item['surname']}', '{$item['lastname']}', '{$item['gender']}', '{$item['description']}', '{$item['url']}', '{$item['institute']}')";
		$result = $this->db->query($query);
		if($result){
			return $this->db->insert_id;
		} else
			return false;
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
			$google = $this->registry['google'];
			$user = $google->get_user();
			$item = array(
				'published'   => '1',
				'created_by'  => $user['id'],
				'name' 		  => '',
				'surname' 	  => '',
				'lastname' 	  => '',
				'gender' 	  => 'm',
				'description' => '',
				'url' 		  => '',
				'institute'   => '-1',
			);
			$data = array('item'=>$item, 'institutes'=>$this->get_institutes());
			return $data;
		}
	}
	
	function get_item_from_form(){
		$google = $this->registry['google'];
		$user = $google->get_user();
		if(isset($_GET['name']) && $_GET['name']){
			$item = array('name'=>$_GET['name'], 'created_by'=>$user['id'], 'published'=>$_GET['published'], 'surname'=>$_GET['surname'], 'lastname'=>$_GET['lastname'], 'description'=>$_GET['description'], 'url'=>$_GET['url'], 'institute'=>$_GET['institute']);
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
