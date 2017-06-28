<h1><i class="uk-icon-close"></i> Видалення</h1>
<?php
if(isset($data['items']) && $data['items']){
?>
	<form action="/lectors/delete">
		<input type="hidden" name="comfirm" value="true">
		<input type="hidden" name="ids" value="<?php echo $data['ids']; ?>">
		<div class="uk-panel uk-panel-box">
			<span class="uk-text-bold">Ви впевнені, що бажаєте видалити таких викладачів:</span><br> 
			<?php
			while($lector = $data['items']->fetch_assoc()){
				echo $lector['name'] . ' ' . $lector['surname'] . ' ' . $lector['lastname'] . '<br>';
			}
			echo '<span class="uk-text-bold">?</span>';
			?>
			<div class="uk-clearfix"></div>
			<button class="uk-float-right uk-button uk-button-danger">Видалити</button>
		</div>
	</form>
<?php
}
?>
