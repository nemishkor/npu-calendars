<?php
/*
 * 	$result->groups
 *	$result->auditories
 *  $result->lectors
 *	$result->calendars
 */
?>

<h1>Додати календар</h1>

<div class="uk-margin">
	<div class="uk-form">
		<?php
		if(mysqli_num_rows($result->groups) > 0){
			echo '<select>';
				while($row = mysqli_fetch_assoc($result->groups)) {
					echo '<option value="'.$row->id.'">'.$row->name.'</option>';
				}
			echo '</select>';
		}
		?>
		<?php
		if(mysqli_num_rows($result->auditories) > 0){
			echo '<select>';
				while($row = mysqli_fetch_assoc($result->auditories)) {
					echo '<option value="'.$row->id.'">'.$row->name.'</option>';
				}
			echo '</select>';
		}
		?>
		<?php
		if(mysqli_num_rows($result->lectors) > 0){
			echo '<select>';
				while($row = mysqli_fetch_assoc($result->lectors)) {
					echo '<option value="'.$row->id.'">'.$row->name.'</option>';
				}
			echo '</select>';
		}
		?>
	</div>
</div>
