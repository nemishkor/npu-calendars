<?php
$user = $data['user'];
$params = $user['params'];
?>
<h1><i class="uk-icon-edit"></i> Редагувати профіль</h1>

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

			<div class="uk-panel uk-panel-box uk-margin">
				<h3 class="uk-panel-title">
					Спільний доступ
				</h3>
				<p class="uk-text-muted">Щоб надати повний доступ до Ваших даних (аудиторій, календарів, дисциплін та ін.), вкажіть нижче їхні gmail логіни</p>
				<div id="shared-with-list" class="uk-form-row">
					<?php
					foreach ($user['full_access'] as $email){
						echo '<div class="uk-form-row">
								<input type="text" name="full_access[]" placeholder="example@gmail.com" value="' . $email . '">
								<button class="remove-shared-email uk-button uk-icon-close"></button>
							</div>';
					}
					?>
				</div>
				<div class="uk-form-row">
					<button id="add-shared-email" class="uk-button"><i class="uk-icon-plus"></i> Додати ще email</button>
				</div>
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
				<div class="uk-form-row">
					<label for="start_date">Початок навчання</label>
					<input name="start_date" id="start_date" value="<?php echo $params->start_date; ?>">
				</div>
				<div class="uk-form-row">
					<label for="end_date">Кінець навчання</label>
					<input name="end_date" id="end_date" value="<?php echo $params->end_date; ?>">
				</div>
			</div>
		</div>
	</div>
</form>

<script>
	(function($){
		$(function(){
			$('#<?php echo strtolower($this->registry['controller_name']); ?>-form').submit(function(event){
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
			$('#add-shared-email').click(function(e){
				$('#shared-with-list').append('<div class="uk-form-row"><input type="text" name="full_access[]" placeholder="example@gmail.com"> <button class="remove-shared-email uk-button uk-icon-close"></button></div>');
				e.preventDefault();
			}).click();
			$('.remove-shared-email').click(function(e){
				$(this).parent().remove();
				e.preventDefault();
			});
		});
	})(jQuery);
</script>
