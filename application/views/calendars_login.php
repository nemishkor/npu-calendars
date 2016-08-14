<?php
?>

<div class="box">
	<div class="request">
		<?php
		if (isset($data['authUrl'])) {
			echo "<a class='login' href='" . $data['authUrl'] . "'>Connect Me!</a>";
		} else {
			echo "<a class='logout' href='/calendars/login?logout'>Logout</a>";
		}
		?>
	</div>
	<div class="data">
		<?php 
		echo '<pre>';
		if (isset($data)) {
			var_dump($data);
		}
		echo '</pre>';
		?>
	</div>
</div>

