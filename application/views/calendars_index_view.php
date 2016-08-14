<h1><i class="uk-icon-calendar-o"></i> Календарі</h1>

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
	<?php
	$this->widget('table', array(
			'data'=>$data,
			'hiddenRow'=>array('trashed'=>'1'),
			'hiddenColumn'=>array('trashed', 'created_by'),
			'columnClass'=>array('id'=>'key'),
			'tableClass' => 'uk-table uk-table-hover uk-table-striped uk-table-condensed',
			'filters' => array('created_by'),
		)
	);
	?>
</div>

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

