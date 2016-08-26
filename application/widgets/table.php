<?php
class Widget_Table extends Widget{
	private $data;
	private $tableClass;
	private $filters;

	function __construct($params, $registry){
		$this->data = $params['data'];
		if(!empty($params['tableClass']))
			$this->tableClass = $params['tableClass'];
		if(!empty($params['filters']))
			$this->filters = $params['filters'];
		parent::__construct($params, $registry);
	}
	
	function display(){
		$this->filters();
		$columns = $this->data['fields'];
		if(is_array($this->tableClass))
			$this->tableClass = implode(' ', $this->tableClass);
		$this->tableClass .= ' ' . strtolower($this->registry['controller_name']) . '-table';
		$output = '<table class="'.$this->tableClass.'">';
		if($columns){
			$output .= '<thead><tr>';
			foreach($columns as $column){
				$th_class = 'column-' . $column->name;	
				if(isset($this->params['columnClass'][$column->name]))
					$th_class .= ' ' . $this->params['columnClass'][$column->name];
				if(isset($this->params['hiddenColumn']) && in_array($column->name, $this->params['hiddenColumn']))
					$th_class .= ' uk-hidden';
				$output .= '<th class="' . $th_class . '">'.$column->name.'</th>';
			}
			$output .= '</tr></thead>';
			$output .= '<tbody>';
			foreach($this->data['items'] as $row){
				$trClass = '';
				if(isset($this->params['hiddenRow'])){
					foreach($columns as $column){
						if(isset($this->params['hiddenRow'][$column->name]))
							if($row[$column->name] == $this->params['hiddenRow'][$column->name])
								$trClass .= ' uk-hidden';
					}
				}
				$output .= '<tr class="' . $trClass . '">';
				foreach($columns as $column){
					$cell = $row[$column->name];
					$td_class = 'column-' . $column->name;
					if(isset($this->params['columnClass'][$column->name]))
						$td_class .= ' ' . $this->params['columnClass'][$column->name];	
					if(isset($this->params['hiddenColumn']) && in_array($column->name, $this->params['hiddenColumn']))
						$td_class .= ' uk-hidden';			
					if($column->name == 'name'){
						if(isset($row['link']))
							$output .= '<td class="' . $td_class . '"><a data-uk-tooltip title="Редагувати" href="' . $row['link'] . '">' . $cell . '</a></td>';
						else
							$output .= '<td data-uk-tooltip title="Ви не маєте прав на редагування" class="' . $td_class . '">' . $cell . '</td>';
					} else				
					if($column->name == 'institute'){
						if(isset($cell['link']))
							$output .= '<td class="' . $td_class . '"><a data-uk-tooltip title="Редагувати" href="' . $cell['link'] . '">' . $cell['name'] . '</a></td>';
						else
							$output .= '<td data-uk-tooltip title="Ви не маєте прав на редагування" class="' . $td_class . '">' . $cell['name'] . '</td>';
					} else				
					if($column->name == 'trashed'){
						$td_class .= ' trash-' . $cell;
						$output .= '<td class="' . $td_class . '">' . $cell . '</td>';
					} else		
					if($column->name == 'published'){
						$icon = ($cell == '0') ? 'uk-icon-close uk-text-danger' : 'uk-icon-check';
						$output .= '<td class="' . $td_class . '"><i class="' . $icon . '"></i></td>';
					} else				
					if($column->name == 'gender'){
						$icon = ($cell == 'm') ? 'uk-icon-mars' : 'uk-icon-venus';
						$output .= '<td class="' . $td_class . '"><i class="' . $icon . '"></i></td>';
					} else				
					if($column->name == 'url' || $column->name == 'link')
						$output .= '<td class="' . $td_class . '"><a href="'.$cell.'" target="_blank"><i class="uk-icon-external-link"></i> '.$cell.'</a></td>';
					else if($column->name == 'events'){
						$output .= '<td class="' . $td_class . '">' . $this->display_events($cell) . '</td>';
					} else if($column->name == 'g_calendar_id'){
						$cell_text = ($cell) ? '<span class="uk-text-success"><i class="uk-icon-check"></i></span>' : '<span><i class="uk-icon-close"></i></span>';
                        $output .= '<td class="' . $td_class . '">' . $cell_text . '</td>';
					} else
						$output .= '<td class="' . $td_class . '">' . $cell . '</td>';
				}
				$output .= '</tr>';
			}
			$output .= '</tbody>';
		}
		$output .='</table>';
		if(count($this->data['items']) == 0)
			$output .= '<p class="uk-alert" data-uk-alert><a href="" class="uk-alert-close uk-close"></a>Таблиця пуста</p>';
		echo $output;
	}

	function filters(){
		if(empty($this->filters))
			return;
		echo '<form action="' . $this->registry['controller_name'] . '/index" method="post" class="uk-form">';
		$table_name = $this->registry['model']->get_table_name();
		foreach($this->filters as $filter){
			echo $filter . ' <select name="' . $filter . '">';
			echo '<option>Всі</option>';
			$query = 'SELECT ' . $filter;
			if($filter == 'created_by')
				$query .= ', u.name';
			$query .= ' FROM ' . $table_name;
			if($filter == 'created_by'){
				$query .= ' INNER JOIN users AS u ON u.id=' . $filter;
			}
			$query .= ' WHERE published=1 AND trashed=0';
			$values = $this->registry['db']->query($query);
			if(!empty($values)) {
				while ($row = $values->fetch_array()) {
					echo '<option value="' . $row[0] . '">';
					if ($filter == 'created_by')
						echo $row[1] . ' [' . $row[0] . ']';
					else
						echo $row[0];
					echo '</option>';
				}
			}
			echo '</select>';	
		}
		echo '<form>';
	}
	
	function display_events($events){
		$events = json_decode($events);
		$dayNames = array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб');
		$output = '<div class="uk-grid uk-grid-small uk-grid-divider uk-text-small">';
		foreach($events as $week){
			$output .= '<div class="uk-width-1-2">';
			for($i = 0; $i < 6; $i++){
				$day = $week[$i];
				$lessonsCount = 0;
				foreach($day as $lesson){
					if($lesson[0] != '' && $lesson[1] != '' && $lesson[2] != '' &&
						$lesson[0] != null && $lesson[1] != null && $lesson[2] != null)
						$lessonsCount++;
				}
				$class = ($lessonsCount == 0) ? ' uk-text-muted' : '';
				$output .= '<div class="uk-margin-right uk-float-left' . $class . '">' . $dayNames[$i] . ' - ' . $lessonsCount . '</div>';
			}
			$output .= '</div>';
		}
		$output .= '</div>';
		return $output;
	}
	
}
?>
