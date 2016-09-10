<?php 
class Widget_Toolbar{
	private $params;
	private $registry;
	
	function __construct($params, $registry){
		$this->registry = $registry;
		/*
		 * $params = array - contains rules for buttons.
		 * 		For example: Array('add'=>'0', 'remove'=>'1')
		 */
		$this->params['actions'] = array(
			'view'=>'1', 
			'add'=>'1',  
			'schedules'=>'1',
			'trash'=>'1',
			'delete'=>'1', 
			'publish'=>'1',
			'unpublish'=>'1', 
			'refresh'=>'1', 
			'edit'=>'1',
			'import'=>'1',);
		if(count($params)){
			foreach($params as $key=>$value){
				$this->params['actions'][$key] = $value;
			}
		}
		$google = $this->registry['google'];
		if(!$google->get_user()){
			$this->params['actions']['schedules'] = 0;
		}
		$icons = array(
			'view'=>'th-list', 
			'add'=>'plus', 
			'trash'=>'trash',
			'untrash'=>'trash-o',
			'delete'=>'close', 
			'schedules'=>'clock-o',
			'publish'=>'check', 
			'unpublish'=>'close', 
			'refresh'=>'refresh', 
			'edit'=>'edit',
			'import'=>'file-excel-o');
		$this->params['icons'] = $icons;
		if(isset($params['icons']) && $params['icons']){
			foreach($params['icons'] as $key=>$value){
				$this->params['icons'][$key] = $value;
			}
		}
	}
	
	function check_controller(){
		foreach($this->params['actions'] as $name=>$value){
			$action_name = 'action_' . strtolower($name);
			if(!method_exists($this->registry['controller'], $action_name)){
				$this->params['actions'][$name] = '0';
			}
		}
	}
	
	function load_head(){
		$form_edit_class = strtolower($this->registry['controller_name']) . '-edit-form';
		$form_view_class = strtolower($this->registry['controller_name']) . '-view-form';
		$form_trash_class = strtolower($this->registry['controller_name']) . '-trash-form';
		$form_schedules_class = strtolower($this->registry['controller_name']) . '-add-to-google-form';
		$form_delete_class = strtolower($this->registry['controller_name']) . '-delete-form';
		$table_class = strtolower($this->registry['controller_name']) . '-table';
		$output = '<script>
			jQuery(document).ready(function(){
				if(jQuery(".toolbar .btn-edit, .toolbar .btn-view, .toolbar .btn-publish, .toolbar .btn-unpublish, .toolbar .btn-delete").length){
					// prepare table
					jQuery(".' . $table_class . ' th.key").show().text("");
					jQuery(".' . $table_class. ' tr td.key").each(function(index, dom){
						var key = jQuery(this).text();
						jQuery(this).html(\'<input type="checkbox" name="id" value="\' + key + \'">\').show();
					});
				}
			});
		</script>';
		if($this->params['actions']['edit']){
			$output .= '<script>
			jQuery(document).ready(function(){
				if(jQuery(".toolbar .btn-edit").length){
					// validation and adding action for submit
					jQuery(".toolbar .btn-edit").click(function(){
						var check = jQuery(".' . $table_class . ' td.key :checked");
						if(check.length)
							var id = check.eq(0).attr("value"); 
						else
							var id = "";
						if(id == ""){
							UIkit.notify("Для редагування відмітьте елемент нижче", {status:"warning"});
							event.preventDefault();
						} else {
							jQuery(".' . $form_edit_class . ' [name=id]").val(id);
							jQuery(".' . $form_edit_class . '").submit();
						}
					});
				}
			});
			</script>
			<form class="' . $form_edit_class . ' uk-table" action="/' . $this->registry['controller_name'] . '/edit" method="get">
				<input type="hidden" name="is_new" value="false">
				<input type="hidden" name="id" value="">
			</form>';
		}
		
		if($this->params['actions']['trash']){
			$output .= '<form class="' . $form_trash_class . '" action="/' . $this->registry['controller_name'] . '/trash" method="get">
					<input type="hidden" name="ids">
					<input type="hidden" name="trash" value="1">
				</form>
				<script>
				jQuery(document).ready(function(){
					jQuery(".toolbar .btn-trash").click(function(){
						var ids = "";
						jQuery(".' . $table_class . ' td.key :checked").each(function(index, dom){
							if(index != 0)
								ids += "-";
							ids += jQuery(this).attr("value");
						});
						if(ids != ""){
							if(jQuery(this).hasClass("btn-untrash"))
								jQuery(".' . $form_trash_class . ' [name=trash]").val("0");
							jQuery(".' . $form_trash_class . ' [name=ids]").val(ids);
							jQuery(".' . $form_trash_class . '").submit();
						} else {
							UIkit.notify("Виберіть елементи нижче, які необхідно перемістити у смітник", {status:"warning"});
						}
					});
				});
				</script>';
		}
		
		if($this->params['actions']['delete']){
			$output .= '<form class="' . $form_delete_class . '" action="/' . $this->registry['controller_name'] . '/delete" method="get">
					<input type="hidden" name="ids">
				</form>
				<script>
				jQuery(document).ready(function(){
					jQuery(".toolbar .btn-delete").click(function(){
						var ids = "";
						jQuery(".' . $table_class . ' td.key :checked").each(function(index, dom){
							if(index != 0)
								ids += "-";
							ids += jQuery(this).attr("value");
						});
						if(ids != ""){
							jQuery(".' . $form_delete_class . ' [name=ids]").val(ids);
							jQuery(".' . $form_delete_class . '").submit();
						} else {
							UIkit.notify("Виберіть елементи нижче, які необхідно видалити", {status:"warning"});
						}
					});
				});
				</script>';
		}
		
		if($this->params['actions']['view']){
			$output .= '<form class="' . $form_view_class . '" action="/' . $this->registry['controller_name'] . '/view" method="get">
				    <input type="hidden" name="id">
				</form>
				<script>
				jQuery(document).ready(function(){
					jQuery(".toolbar .btn-view").click(function(){
						var check = jQuery(".' . $table_class . ' td.key :checked");
						if(check.length){
							var id = check.eq(0).attr("value"); 
							jQuery(".' . $form_view_class . ' [name=id]").val(id);
							jQuery(".' . $form_view_class . '").submit();
						} else {
							UIkit.notify("Для редагування відмітьте елемент нижче", {status:"warning"});
							event.preventDefault();
						}
					});
				});
				</script>';
		}
		
		if($this->params['actions']['schedules']){
			$output .= '<form class="' . $form_schedules_class . '" action="/' . $this->registry['controller_name'] . '/schedules" method="get">
				    <input type="hidden" name="id">
				</form>
				<script>
				jQuery(document).ready(function(){
					jQuery(".toolbar .btn-schedules").click(function(){
						var check = jQuery(".' . $table_class . ' td.key :checked");
						if(check.length){
							var id = check.eq(0).attr("value"); 
							jQuery(".' . $form_schedules_class . ' [name=id]").val(id);
							jQuery(".' . $form_schedules_class . '").submit();
						} else {
							UIkit.notify("Відмітьте елемент нижче", {status:"warning"});
							event.preventDefault();
						}
					});
				});
				</script>';
		}
		echo $output;
	}
	
	public function display(){
		$this->check_controller();
		$this->load_head();
		$output = '<div class="toolbar uk-margin uk-margin-bottom uk-width-1-1 uk-button-group">';
		if($this->params['actions']['view']){
			$output .= '<button class="btn-view uk-button"><i class="uk-icon-' . $this->params['icons']['view'] . '"></i> Відкрити</button>';
		}
		if($this->params['actions']['add']){
			$output .= '<a href="/' . $this->registry['controller_name'] . '/edit" class="btn-add uk-button uk-button-success"><i class="uk-icon-' . $this->params['icons']['add'] . '"></i> Додати</a>';
		}
		if($this->params['actions']['schedules']){
			$output .= '<button class="btn-schedules uk-button"><i class="uk-icon-' . $this->params['icons']['schedules'] . '"></i> Розклади</a>';
		}
		if($this->params['actions']['trash']){
			$output .= '<button href="/' . $this->registry['controller_name'] . '/trash" class="btn-trash uk-button uk-button-warning"><i class="uk-icon-' . $this->params['icons']['trash'] . '"></i> У смітник</button>';
			$output .= '<button href="/' . $this->registry['controller_name'] . '/trash" class="btn-trash btn-untrash uk-button uk-button-warning"><i class="uk-icon-' . $this->params['icons']['untrash'] . '"></i> Відновити із смітника</button>';
		}
		if($this->params['actions']['delete']){
			$output .= '<button href="/' . $this->registry['controller_name'] . '/delete" class="btn-delete uk-button uk-button-danger"><i class="uk-icon-' . $this->params['icons']['delete'] . '"></i> Видалити</button>';
		}
		if($this->params['actions']['publish']){
			$output .= '<a href="/' . $this->registry['controller_name'] . '/publish" class="btn-publish uk-button"><i class="uk-icon-' . $this->params['icons']['publish'] . '"></i> Увімкнути</a>';
		}
		if($this->params['actions']['unpublish']){
			$output .= '<a href="/' . $this->registry['controller_name'] . '/unpublish" class="btn-unpublish uk-button"><i class="uk-icon-' . $this->params['icons']['unpublish'] . '"></i> Вимкнути</a>';
		}
		if($this->params['actions']['refresh']){
			$output .= '<a href="/' . $this->registry['controller_name'] . '/refresh" class="btn-refresh uk-button"><i class="uk-icon-' . $this->params['icons']['refresh'] . '"></i> Оновити</a>';
		}
		if($this->params['actions']['edit']){
			$output .= '<button href="/' . $this->registry['controller_name'] . '/edit" class="btn-edit uk-button uk-button-primary"><i class="uk-icon-' . $this->params['icons']['edit'] . '"></i> Редагувати</button>';
		}
		if($this->params['actions']['import']){
			$output .= '<a href="/' . $this->registry['controller_name'] . '/import" class="uk-hidden btn-import uk-button"><i class="uk-icon-' . $this->params['icons']['import'] . '"></i> Імпорт csv</a>';
		}
		$output .= '</div>';
		echo $output;
	}
}
?>
