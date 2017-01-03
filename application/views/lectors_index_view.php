<?php
if($data){
	$table_class = strtolower($this->registry['controller_name']) . '-table';
?>
<h1><i class="uk-icon-mortar-board"></i> Викладачі</h1>

<div class="uk-margin uk-form">
	<?php
	$this->widget('toolbar', array("add" => '1'));
	?>
	<div class="uk-clearfix"></div>
	<button class="trash-toggle uk-float-right uk-button" type="button" data-uk-button>Показати/приховати елементи в корзині</button>
	<div class="uk-clearfix"></div>
	<h3>Ваші дані</h3>
	<?php
	$this->widget('table', array(
		'data'          => $data,
		'hiddenRow'     => array('trashed'=>'1'),
		'hiddenColumn'  => array('trashed'),
		'columnClass'   => array('id'=>'key'),
		'tableClass'    => $table_class . ' uk-table uk-table-hover uk-table-striped uk-table-condensed'
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
			'hiddenColumn'  => array('trashed'),
			'columnClass'   => array('id'=>'key'),
			'tableClass'    => $table_class . ' uk-table uk-table-hover uk-table-striped uk-table-condensed'
		)
	);
	?>
</div>
<?php
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

<pre><?php var_dump($data); ?></pre>

