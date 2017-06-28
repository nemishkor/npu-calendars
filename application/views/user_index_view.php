<?php
$user = $data['user'];
$group = $data['user_group'];
?>
<h1><span class="uk-icon-user"></span> Ваш обліковий запис</h1>

<div class="uk-margin">
	<a href="/user/index?logout" class="uk-button uk-button-danger">Вихід</a>
	<a href="/user/edit" class="uk-button"><i class="uk-icon-cog"></i> Редагувати</a>
	<p>
		<span class="uk-text-muted uk-h6">id = 
		<?php
		echo $user['id'];
		?>
		</span><br>
		<span>Gmail: <?php echo '<span class="uk-h3">' . $user['email'] . '</span>'; ?></span><br>
		<span>Ім'я: <?php if($user['name']) echo $user['name']; else echo 'не вказане'; ?></span><br>
		<span>Група: <?php echo $group['name']; ?></span>
	</p>
	<?php
	$permissions = json_decode($group['permissions']);
	$column_count = intval(substr_count($group['permissions'],':') / 4);
	?>
    <p>Права</p>
	<div class="uk-grid">
        <div class="uk-width-large-1-4">
        <ul>
            <?php
            $num = 1;
            foreach ($permissions as $name=>$value){
                if($num % $column_count == 0)
                    echo '</ul></div><div class="uk-width-large-1-4"><ul>';
                echo '<li>' . $name . ' - ';
                if($value)
                    echo '<i class="uk-icon-check uk-text-success"></i>';
                else
                    echo '<i class="uk-icon-close uk-text-danger"></i>';
                echo '</li>';
                $num++;
            }
            ?>
        </ul>
        </div>
    </div>
</div>
