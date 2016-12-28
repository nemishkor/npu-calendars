<?php
$item = $data['item'];
/* id
 * published
 * trashed
 * created_by
 * name
 * institute
 */
$institutes = $data['institutes'];
/* id
 * name 
 * description
 * created_by
 * description
 * url
 */
var_dump($data['access']);
?>

<script>
jQuery(document).ready(function(){
	jQuery('.form-validate').submit(function(event){
		var submit = true;
		jQuery(this).find('.required').each(function(index, dom){
			if(jQuery(this).val() == ''){
				jQuery(this).addClass('uk-form-danger');
				submit = false;
		    }
		});
		jQuery(this).find('select').each(function(){
			if(jQuery(this).val() == '-1')
				submit = false;
		});
		if(submit == false){
			jQuery(this).find('.uk-alert').show(400);
			event.preventDefault();
		}
	}).find('input').click(function(){jQuery(this).removeClass('uk-form-danger');});
});
</script>

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


<h1>
	<?php 
	if(isset($item['is_new']) && ($item['is_new'] == 'true'))
		echo '<i class="uk-icon-plus"></i> Додати аудиторію';
	else
		echo '<i class="uk-icon-edit"></i> Редагувати аудиторію';
	?>
</h1>
<form id="<?php echo strtolower($this->registry['controller_name']); ?>-form" class="uk-form form-validate" action="/<?php echo strtolower($this->registry['controller_name']); ?>/edit" method="get" data-uk-margin>
	<p class="uk-alert uk-alert-warning" data-uk-alert style="display:none;"><i class="uk-close uk-alert-close"></i> Помилка. Перевірте всі дані перез збереженням</p>
	<input type="hidden" name="action">
	<?php
	if(isset($item['id']) && ($item['is_new'] == 'false')){
	?>
	<input type="hidden" name="id" value="<?php echo $item['id']; ?>">
	<?php
	}
	?>
	<div class="uk-form-row">
		<input class="required" type="text" name="name" placeholder="Ім'я" value="<?php echo $item['name']; ?>"> <i class="uk-icon-star" data-uk-tooltip="{pos:'right'}" title="Обов'язкове поле"></i>
	</div>
	<div class="uk-form-row">
		<select class="required" name="institute">
			<option value="-1">-- Виберіть інститут --</option>
			<?php
			foreach($institutes as $institute){
			?>
				<option value="<?php echo $institute['id']; ?>" <?php if($institute['id'] == $item['institute']) echo 'selected'; ?>>
				    <?php echo $institute['name']; ?>
				</option>
			<?php
			}
			?>
		</select> <i class="uk-icon-star" data-uk-tooltip="{pos:'right'}" title="Обов'язкове поле"></i>
	</div>
	<input type="radio" name="published" value="1" <?php if($item['published'] == '1') echo 'checked' ?>> Увімкнений<br>
	<input type="radio" name="published" value="0" <?php if($item['published'] == '0') echo 'checked' ?>> Вимкнений
	<div class="uk-form-row">
		<button type="submit" type="button" data-uk-button class="btn-save btn-action uk-button uk-button-primary"><i class="uk-icon-save"></i> Зберегти</button>
		<button type="submit" type="button" data-uk-button class="btn-close btn-action uk-button"><i class="uk-icon-close"></i> Зберегти та закрити</button>
		<button type="submit" type="button" data-uk-button class="btn-new btn-action uk-button"><i class="uk-icon-plus"></i> Зберегти та створити</button>
	</div>
</form>

<pre>
<?php var_dump($data); ?>
</pre>
