<?php
include "application/widgets/table.php";
$table = new Table_Widget($data, array('tableClass' => 'uk-table uk-table-hover uk-table-striped uk-table-condensed'));
?>
<h1>Календарі</h1>

<div class="uk-margin">
	<?php
	$table->display();
	?>
</div>
