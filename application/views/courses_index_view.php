<?php
if($data){
	$table_class = strtolower($this->registry['controller_name']) . '-table';
?>
<h1><i class="uk-icon-book"></i> Дисципліни</h1>

<div class="uk-margin uk-form">
	<?php
	$this->widget('toolbar', array());
	?>
	<div class="uk-clearfix"></div>
	<button class="trash-toggle uk-float-right uk-button" type="button" data-uk-button>Показати/приховати елементи в корзині</button>
	<div class="uk-clearfix"></div>
	<?php
	$this->widget('table', array(
		'data'=>$data, 
		'hiddenRow'=>array('trashed'=>'1'), 
		'hiddenColumn'=>array('trashed', 'created_by'), 
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

<p class="uk-text-muted">var_dump($data)</p>
<pre><?php var_dump($data); ?></pre>

