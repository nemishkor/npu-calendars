<?php
class Crud_Controller extends Controller{
	
	protected $view_file_name;
	
	function __construct($registry){
		$model_name = 'Model_' . ucfirst($registry['controller_name']);
		$this->model = new $model_name($registry);
		$this->view_file_name = strtolower($registry['controller_name']) . '_' . $registry['action_name'] . '_view.php';
		parent::__construct($registry);
	}
		
	function action_index(){
		$user = $this->google->get_user();
		$data = $this->model->get_data($user['id'], $user['email']);
		$this->view->generate($this->view_file_name, 'template_view.php', $data);
	}
	
	function action_add(){
		
	}
	
	function action_edit(){
		// saving
		$item_id = null;
		if(isset($_GET['id']) && $_GET['id'])
			$item_id = $_GET['id'];
		if(isset($_GET['action'])){
			if(isset($_GET['id']) && $_GET['id']){
				$item_id = $this->model->save();
			} else {
				$item_id = $this->model->create();
			}
			if($_GET['action'] == 'new')
				$item_id = null;
		}
		// get data and open edit form
		if(isset($_GET['action']) && $_GET['action'] == 'new' || !isset($_GET['action']) || isset($_GET['action']) && $_GET['action'] == 'save'){
			$data = $this->model->get_edit_item($item_id);
			$this->view->generate($this->view_file_name, 'template_view.php', $data);
		} else // or open index page
			header('Location:' . $this->registry['host'] . strtolower($this->registry['controller_name']) . '/index');
	}
	
	function action_trash(){
		$data = array();
		if(isset($_GET['ids']) && $_GET['ids']){ // it's OK
			$data['trash'] = $_GET['trash']; // "true" - to trash, "false" - from trash
			$ids = str_replace('-',',',$_GET['ids']); // prepare ids
			if(isset($_GET['comfirm']) && ($_GET['comfirm'] == 'true')){ // if we comfirm moving to trash
				$result = $this->model->trash($ids, $data['trash']);
				if($result){
					if($data['trash'])
						$this->registry->set('info', 'Елементи переміщені у смітник');
					else
						$this->registry->set('info', 'Елементи відновлено із смітника');
				} else {
					if($data['trash'])
						$this->registry->set('error', 'Виникли проблеми із переміщенням у смітник');
					else
						$this->registry->set('error', 'Виникли проблеми із відновленням зі смітника');
				}
			} else {
				$data['items'] = $this->model->get_items($ids);
				$data['ids'] = $_GET['ids'];
			} 
		}
		$this->view->generate($this->view_file_name, 'template_view.php', $data);
	}
	
	function action_delete(){
		$data = array();
		if(isset($_GET['ids']) && $_GET['ids']){ // it's OK
			$ids = str_replace('-',',',$_GET['ids']); // prepare ids
			if(isset($_GET['comfirm']) && ($_GET['comfirm'] == 'true')){ // if we comfirm deleting
				$result = $this->model->delete($ids);
				if($result)
					$this->registry->set('info', 'Елементи видалені');
				else
					$this->registry->set('error', 'Виникли проблеми із видаленням');
			} else { // else show request for comfirm with names, surnames and lastnames
				$data['items'] = $this->model->get_items($ids);
				$data['ids'] = $_GET['ids'];
			} 
		}
		$this->view->generate($this->view_file_name, 'template_view.php', $data);
	}
	
}
?>
