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
	<?php
	$this->widget('table', array(
		'data'=>$data, 
		'hiddenRow'=>array('trashed'=>'1'), 
		'hiddenColumn'=>array('trashed'), 
		'columnClass'=>array('id'=>'key'), 
		'tableClass' => $table_class . ' uk-table uk-table-hover uk-table-striped uk-table-condensed'
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
