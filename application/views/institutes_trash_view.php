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
	<form action="/<?php echo $this->registry['controller_name']; ?>/trash">
		<input type="hidden" name="comfirm" value="true">
		<input type="hidden" name="trash" value="<?php echo $data['trash']; ?>">
		<input type="hidden" name="ids" value="<?php echo $data['ids']; ?>">
		<div class="uk-panel uk-panel-box">
			<span class="uk-text-bold">
				<?php
				if($data['trash'])
					echo 'Ви впевнені, що бажаєте перемістити у смітник такі інститути:';
				else
					echo 'Ви впевнені, що бажаєте відновити зі смітника такі інститути:';
				?>
			</span><br> 
			<?php
			while($item = $data['items']->fetch_assoc()){
				echo $item['name'] . '<br>';
			}
			echo '<span class="uk-text-bold">?</span>';
			?>
			<div class="uk-clearfix"></div>
			<?php
			if($data['trash'])
				echo '<button class="uk-float-right uk-button uk-button-danger">Перемістити у смітник</button>';
			else
				echo '<button class="uk-float-right uk-button">Відновити</button>';
			?>
		</div>
	</form>
<?php
}
?>
