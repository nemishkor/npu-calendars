<?php
$user = $data['user'];
$params = $user['params'];
?>
<h1><i class="uk-icon-edit"></i> Редагувати профіль</h1>

<script>
jQuery(document).ready(function(){
	jQuery('#<?php echo strtolower($this->registry['controller_name']); ?>-form').submit(function(event){
		var form = jQuery(this);
		var action = form.find('[name=action]');
		var btn = form.find('.btn-action.uk-active');
		if(btn.hasClass('btn-save'))
			action.val('save');
		else
		if(btn.hasClass('btn-close'))
			action.val('close');
		else
		if(btn.hasClass('btn-new'))
			action.val('new');
	});
});
</script>

<form id="<?php echo strtolower($this->registry['controller_name']); ?>-form" class="uk-form" action="/user/edit" method="POST">
    <input type="hidden" name="action" value="save">
    <div class="uk-grid">
		<div class="uk-width-1-2">
			<div class="uk-form-row">
				<input type="text" name="name" value="<?php echo $user['name']; ?>" placeholder="Вкажіть Ваше ім'я">
			</div>
			<div class="uk-form-row">
				<select disabled name="group_id">
					<option value="<?php echo $user['group_id']; ?>"><?php echo $user['group_name']; ?></option>
				</select>
				<i title="Ви не можете змінити свою групу" data-uk-tooltip class="uk-icon-info"></i>
			</div>
			<div class="uk-form-row">
				<button type="submit" type="button" data-uk-button class="btn-save btn-action uk-button uk-button-primary"><i class="uk-icon-save"></i> Зберегти</button>
				<button type="submit" type="button" data-uk-button class="btn-close btn-action uk-button"><i class="uk-icon-close"></i> Зберегти та закрити</button>
			</div>
		</div>
		<div class="uk-width-1-2">
			<div class="uk-panel uk-panel-box">
				<h3 class="uk-panel-title">
					Параметри за замовчування
				</h3>
				<p class="uk-text-muted">Ці налаштування будуть застосовуватися до всіх нових даних, які Ви створюєте, за замовчуванням. Всі параметри можна змінювати під час редагування відповідних даних.</p>
				<div class="uk-form-row">
					<input type="checkbox" name="dual_week" <?php if($params->dual_week) echo "checked"; ?>> 2-х тижневий розклад
				</div>
				<div class="uk-form-row">
					<span>Часовий пояс </span>
					<select name="timezone">
						<?php
						foreach($data['timezones'] as $timezone){
							if($timezone == $params->timezone)
								$checked = 'selected="selected"';
							else
								$checked = '';
							echo '<option value="' . $timezone . '" ' . $checked . '>' . $timezone . '</option>';
						}
						?>
					</select>
				</div>
			</div>
		</div>
	</div>
</form>
