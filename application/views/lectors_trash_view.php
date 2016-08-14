<h1><i class="uk-icon-trash"></i> 
	<?php
	if($data['trash'])
		echo 'Переміщення у смітник';
    else
		echo 'Відновлення зі смітника';
    ?>
</h1>
<?php
if(isset($data['items']) && $data['items']){
?>
	<form action="/lectors/trash">
		<input type="hidden" name="comfirm" value="true">
		<input type="hidden" name="trash" value="<?php echo $data['trash']; ?>">
		<input type="hidden" name="ids" value="<?php echo $data['ids']; ?>">
		<div class="uk-panel uk-panel-box">
			<span class="uk-text-bold">
				<?php
				if($data['trash'])
					echo 'Ви впевнені, що бажаєте перемістити у смітник таких викладачів:';
				else
					echo 'Ви впевнені, що бажаєте відновити зі смітника таких викладачів:';
				?>
			</span><br> 
			<?php
			while($lector = $data['items']->fetch_assoc()){
				echo $lector['name'] . ' ' . $lector['surname'] . ' ' . $lector['lastname'] . '<br>';
			}
			echo '<span class="uk-text-bold">?</span>';
			?>
			<div class="uk-clearfix"></div>
			<?php
			if($data['trash'])
				echo '<button class="uk-float-right uk-button uk-button-danger">Видалити</button>';
			else
				echo '<button class="uk-float-right uk-button">Відновити</button>';
			?>
		</div>
	</form>
<?php
}
?>
