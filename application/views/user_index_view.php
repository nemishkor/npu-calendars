<?php
$user = $data['user'];
?>
<h1><span class="uk-icon-user"></span> Ваш обліковий запис</h1>

<div class="uk-margin">
	<a href="/user/login?logout" class="uk-button uk-button-danger">Вихід</a>
	<a href="/user/edit" class="uk-button"><i class="uk-icon-cog"></i> Редагувати</a>
	<p>
		<span class="uk-text-muted uk-h6">id = 
		<?php
		echo $user['id'];
		?>
		</span><br>
		<span>Gmail: <?php echo '<span class="uk-h3">' . $user['email'] . '</span>'; ?></span><br>
		<span>Ім'я: <?php if($user['name']) echo $user['name']; else echo 'не вказане'; ?></span><br>
		<span>Група: <?php echo $user['group_name']; ?></span>
	</p>
</div>
