<h1>
	<i class="uk-icon-calendar-o"></i>&nbsp;
	<?php
	if($data['user'] == $data['user_calendars']['parameters']['user_id'])
		echo 'Ваші календарі';
	else
		echo 'Календарі користувача ' . $data['user_name'];
	?>
</h1>
<div class="uk-margin uk-form">
	<?php
	$this->widget(
		'toolbar', 
		array(
			'icons'=>array(
				'add'=>'calendar-plus-o',
				'publish'=>'calendar-check-o',
				'unpublish'=>'calendar-times-o',
				'delete'=>'calendar-minus-o',
			)
		)
	);
	?>
	<div class="uk-clearfix"></div>
	<button class="trash-toggle uk-float-right uk-button" type="button" data-uk-button>Показати/приховати елементи в корзині</button>
	<div class="uk-clearfix"></div>
	<h3>Ваші дані</h3>
	<?php
	$this->widget('table', array(
			'data'=>$data['user_calendars'],
			'hiddenRow'=>array('trashed'=>'1'),
			'hiddenColumn'=>array('trashed', 'created_by', 'timezone'),
			'columnClass'=>array('id'=>'key'),
			'tableClass' => 'uk-table uk-table-hover uk-table-striped uk-table-condensed',
			'filters' => array('created_by'),
		)
	);
	?>
	<h3>Спільні дані з іншими користувачами</h3>
	<?php
	$shared_data = array(
		'fields' => $data['shared_fields'],
		'items' => $data['shared_items']
	);
	$this->widget('table', array(
			'data'          => $shared_data,
			'hiddenRow'     => array('trashed'=>'1'),
			'hiddenColumn'  => array('trashed', 'created_by', 'timezone', 'full_access'),
			'columnClass'   => array('id'=>'key'),
			'tableClass'    => $table_class . ' uk-table uk-table-hover uk-table-striped uk-table-condensed'
		)
	);
	?>
</div>
<?php
if($data['google_calendars']){
	?>
    <h1><i class="uk-icon-google"></i> Ваші Google календарі</h1>
	<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
		<thead>
		    <tr>
				<th>Назва</th>
				<th>Тип доступу</th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($data['google_calendars']['modelData']['items'] as $item){
			?>
			<tr>
				<td>
					<div style="
					    display: inline-block;
					    margin-right: 10px;
					    width:1rem;
					    height:1rem;
					    background-color:<?php echo $item['backgroundColor']; ?>">
					</div>
					<span>
						<?php echo $item['summary']; ?>
					</span>
				</td>
				<td>
					<?php
					if($item['accessRole'] == 'owner') {
						echo '<span title="Ви маєте повний доступ"
					   data-uk-tooltip> - власник</span>';
					} elseif($item['accessRole'] == 'reader') {
						echo '<span class="uk-text-muted" title="Ви не можете редагувати цей календар"
					   data-uk-tooltip>читач</span>';
					} else {
						echo '<span class="uk-text-muted" title="unknown accessRole"
					   data-uk-tooltip>?</span>';
					}
					?>
				</td>
			</tr>
		<?php
		}
		?>
		</tbody>
	</table>
    <?
}
?>

<script>
jQuery(document).ready(function(){
	jQuery('.trash-toggle').click(function(){
		if(!jQuery(this).hasClass('uk-active'))
			jQuery('.<?php echo $table_class; ?> .trash-1').parent().add('.column-trashed').removeClass('uk-hidden')
		else
			jQuery('.<?php echo $table_class; ?> .trash-1').parent().add('.column-trashed').addClass('uk-hidden')
	});
});
</script>

