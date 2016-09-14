<?php
$calendar = $data['calendar'];
$params = $data['params'];

if(isset($_GET['id']) && $_GET['id'])
	echo '<h1><i class="uk-icon-edit"></i> Редагувати календар</h1>'; 
else
	echo '<h1><i class="uk-icon-plus"></i> Додати календар</h1>'; 
?>

<style>
.uk-modal td:hover{
	cursor: pointer;
}
.uk-table tr{
	transition: .4s all;
}
.uk-table tr.selected{
    background-color: #35b3ee;
    color: #ffffff;
}
</style>

<script>
	var auditories = <?php echo json_encode($data['auditories']); ?>;
	var courses = <?php echo json_encode($data['courses']); ?>;
	var groups = <?php echo json_encode($data['groups']); ?>;
	var institutes = <?php echo json_encode($data['institutes']); ?>;
	var lectors = <?php echo json_encode($data['lectors']); ?>;
	var events = <?php if($calendar) echo json_encode($calendar['events']); else echo 'null'; ?>;
	
	// create events UI and fill data to them
	function init(){
		var dayNames = ['Понеділок', 'Вівторок', 'Середа', 'Четверг', 'П\'ятниця', 'Субота'];
		for (var i = 0; i < 2; i++){
			var week = $('<div class="week uk-grid uk-grid-small" data-uk-margin></div>');
			for (var j = 0; j < 6; j++){
				var day = $('<div class="day-wrapper uk-width-1-1 uk-width-small-1-3" data-uk-margin><div class="day uk-panel uk-panel-box"><span class="day-name uk-panel-title uk-muted uk-h3"></span><hr></div></div>');
				day.find('.day-name').text(dayNames[j]);
				for(var k = 0; k < 9; k++){
					var lesson = $('\
					<div class="lesson">\
						<div class="uk-grid" data-uk-margin>\
							<div class="lesson-header uk-width-1-1">\
								<span class="lesson-number"></span>\
								<button class="lesson-add uk-button uk-button-small uk-button-success" type="button">Додати</button>\
								<button class="lesson-edit uk-button uk-button-small uk-button-primary" type="button" disabled>Редагувати</button>\
								<button class="lesson-delete uk-button uk-button-small uk-button-danger" type="button" disabled>Видалити</button>\
							</div>\
						<div class="lesson-values uk-width-1-1" style="display:none">\
							<i class="uk-icon-users"></i> <span class="lesson-group"></span><br>\
							<i class="uk-icon-book"></i> <span class="lesson-course"></span><br>\
							<i class="uk-icon-mortar-board"></i> <span class="lesson-lector"></span><br>\
							<i class="uk-icon-cube"></i> <span class="lesson-auditory"></span>\
						</div>\
					</div>');
					lesson.find('.lesson-number').text((k + 1) + ' - пара');
					day.find('.day').append(lesson);
					if(k != 8)
						day.find('.day').append('<hr>');
				}
				week.append(day);
			}
			$('.weeks').append($('<li></li>').append(week));
		}
		// fill form data from server
		if(events){
			for (var i = 0; i < 2; i++){
				for (var j = 0; j < 6; j++){
					for (var k = 0; k < 9; k++){
						if(events[i][j][k][0] != '' && events[i][j][k][1] != '' && events[i][j][k][2] != '' &&
						events[i][j][k][0] != null && events[i][j][k][1] != null && events[i][j][k][2] != null){
							var lesson = $('.week').eq(i).find('.day').eq(j).find('.lesson').eq(k);
							var group = getGroup(events[i][j][k][0]);
							var course = getCourse(events[i][j][k][1]);
							var lector = getLector(events[i][j][k][2]);
							var auditory = getAuditory(events[i][j][k][3]);
							lesson
								.data('group', group['id'])
								.data('course', course['id'])
								.data('lector', lector['id'])
								.data('auditory', auditory['id'])
								.find('.lesson-add')
								.attr('disabled', '')
								.siblings()
								.removeAttr('disabled');
							lesson.find('.lesson-group').text(group['name']);
							lesson.find('.lesson-course').text(course['name']);
							lesson.find('.lesson-lector').text(lector['name']);
							lesson.find('.lesson-auditory').text(auditory['name']);
							lesson.find('.lesson-values').show(400);
						}
					}
				}
			}
		}
	}
	
	function getGroup(id){
		for(var j = 0; j < groups.length; j++){
			if(id == groups[j]['id']){
				return groups[j];
				break;
			}
		}
	}
	
	function getCourse(id){
		for(var j = 0; j < courses.length; j++){
			if(id == courses[j]['id']){
				return courses[j];
				break;
			}
		}
	}
	
	function getLector(id){
		for(var j = 0; j < lectors.length; j++){
			if(id == lectors[j]['id']){
				return lectors[j];
				break;
			}
		}
	}
	
	function getAuditory(id){
		for(var j = 0; j < auditories.length; j++){
			if(id == auditories[j]['id']){
				return auditories[j];
				break;
			}
		}
	}
	
	
	
	
	
	
	// START editing events
	
	group = null;
	course = null;
	lector = null;
	auditory = null;
	lesson = null;
	
	// adding data to tables in modal
	function addGroups(){
		$('#modal-groups tbody').html('');
		for(var i = 0; i < groups.length; i++){
			var row = $('<tr></tr>');
			row.data('id', groups[i]['id']).attr('id', groups[i]['id']);
			row.append('<td>' + groups[i]['name'] + '</td>');
			var instituteName = 'can\' find institute name';
			for(var j = 0; j < institutes.length; j++){
				if(groups[i]['institute'] == institutes[j]['id']){
				    instituteName = institutes[j]['name'];
				    break;
				}
			}
			row.append('<td>' + instituteName + '</td>');
			$('#modal-groups tbody').append(row);
		}
		$('#modal-groups tbody tr').on('click', function(){
			$(this).addClass('selected').siblings().removeClass('selected');
		});
	}
	function addCourses(){
		$('#modal-courses tbody').html('');
		for(var i = 0; i < courses.length; i++){
			var row = $('<tr></tr>');
			row.data('id', courses[i]['id']).attr('id', courses[i]['id']);
			row.append('<td>' + courses[i]['name'] + '</td>');
			row.append('<td>' + courses[i]['description'] + '</td>');
			$('#modal-courses tbody').append(row);
		}
		$('#modal-courses tbody tr').on('click', function(){
			$(this).addClass('selected').siblings().removeClass('selected');
		});
	}
	function addLectors(){
		$('#modal-lectors tbody').html('');
		for(var i = 0; i < lectors.length; i++){
			var row = $('<tr></tr>');
			row.data('id', lectors[i]['id']).attr('id', lectors[i]['id']);
			row.append('<td>' + lectors[i]['name'] + '</td>');
			row.append('<td>' + lectors[i]['surname'] + '</td>');
			row.append('<td>' + lectors[i]['lastname'] + '</td>');
			row.append('<td>' + lectors[i]['description'] + '</td>');
			var instituteName = 'can\' find institute name';
			for(var j = 0; j < institutes.length; j++){
				if(lectors[i]['institute'] == institutes[j]['id']){
				    instituteName = institutes[j]['name'];
				    break;
				}
			}
			row.append('<td>' + instituteName + '</td>');
			$('#modal-lectors tbody').append(row);
		}
		$('#modal-lectors tbody tr').on('click', function(){
			$(this).addClass('selected').siblings().removeClass('selected');
		});
	}
	function addAudituries(){
		$('#modal-auditories tbody').html('');
		for(var i = 0; i < auditories.length; i++){
			var row = $('<tr></tr>');
			row.data('id', auditories[i]['id']).attr('id', auditories[i]['id']);
			row.append('<td>' + auditories[i]['name'] + '</td>');
			var instituteName = 'can\' find institute name';
			for(var j = 0; j < institutes.length; j++){
				if(auditories[i]['institute'] == institutes[j]['id']){
				    instituteName = institutes[j]['name'];
				    break;
				}
			}
			row.append('<td>' + instituteName + '</td>');
			$('#modal-auditories tbody').append(row);
		}
		$('#modal-auditories tbody tr').on('click', function(){
			$(this).addClass('selected').siblings().removeClass('selected');
		});
	}
	
	// steps when add or edit lesson 
	function add_lesson0(event){
		var btn = event.delegateTarget;
		var modal = UIkit.modal('#modal-groups');
		addGroups();
		if(group)
			$('#modal-groups tr[id=' + group + ']').click();
		modal.show();
	}
	function add_lesson1(event){
		var btn = event.delegateTarget;
		var selected = $('#modal-groups tbody tr.selected');
		if($(btn).hasClass('btn-cancel'))
		    addLessonCancel();
		if($(btn).hasClass('btn-save'))
			if(selected.length){
			    var modal = UIkit.modal('#modal-groups');
			    modal.hide();
				group = selected.data('id');
				addCourses();
		        if(course)
			        $('#modal-courses tr[id=' + course + ']').click();
				var modal = UIkit.modal('#modal-courses');
				modal.show();
			} else {
				UIkit.notify("Натисніть на рядок в таблиці для вибору", {status:"warning"});
			}
	}
	function add_lesson2(event){
		var btn = event.delegateTarget;
		var selected = $('#modal-courses tbody tr.selected');
		if($(btn).hasClass('btn-cancel'))
		    addLessonCancel();
		if($(btn).hasClass('btn-save'))
			if(selected.length){
			    var modal = UIkit.modal('#modal-courses');
			    modal.hide();
			    console.log("selected.data('id') = " + selected.data('id') + " in add_lesson1(event)");
				course = selected.data('id');
				addLectors();
		        if(lector)
			        $('#modal-lectors tr[id=' + lector + ']').click();
				var modal = UIkit.modal('#modal-lectors');
				modal.show();
			} else {
				UIkit.notify("Натисніть на рядок в таблиці для вибору", {status:"warning"});
			}
	}
	function add_lesson3(event){
		var btn = event.delegateTarget;
		var selected = $('#modal-lectors tbody tr.selected');
		if($(btn).hasClass('btn-cancel'))
		    addLessonCancel();
		if($(btn).hasClass('btn-save'))
			if(selected.length){
			    var modal = UIkit.modal('#modal-lectors');
			    modal.hide();
				lector = selected.data('id');
				addAudituries();
		        if(auditory)
			        $('#modal-auditories tr[id=' + auditory + ']').click();
				var modal = UIkit.modal('#modal-auditories');
				modal.show();
			} else {
				UIkit.notify("Натисніть на рядок в таблиці для вибору", {status:"warning"});
			}
	}
	function add_lesson4(event){
		console.log('add_lesson3()');
		console.log(lesson);
		var btn = event.delegateTarget;
		var selected = $('#modal-auditories tbody tr.selected');
		if($(btn).hasClass('btn-cancel'))
		    addLessonCancel();
		if($(btn).hasClass('btn-save'))
			if(selected.length){
				var modal = UIkit.modal('#modal-auditories');
				modal.hide();
				auditory = selected.data('id');
				addLessonSave();
			} else {
				UIkit.notify("Натисніть на рядок в таблиці для вибору", {status:"warning"});
			}
	}
	
	function addLessonSave(){
		lesson
			.data('group', group)
			.data('course', course)
			.data('lector', lector)
			.data('auditory', auditory);
		var groupName, courseName, lectorName, auditoryName;
		for(var i = 0; i < groups.length; i++){
			if(groups[i]['id'] == group){
				groupName = groups[i]['name'];
				break;
			}
		}
		for(var i = 0; i < courses.length; i++){
			if(courses[i]['id'] == course){
				courseName = courses[i]['name'];
				break;
			}
		}
		for(var i = 0; i < lectors.length; i++){
			if(lectors[i]['id'] == lector){
				lectorName = lectors[i]['name'];
				if(lectors[i]['surname'])
				    lectorName += ' ' + lectors[i]['surname'];
				if(lectors[i]['lastname'])
				    lectorName += ' ' + lectors[i]['lastname'];
				break;
			}
		}
		for(var i = 0; i < auditories.length; i++){
			if(auditories[i]['id'] == auditory){
				auditoryName = auditories[i]['name'];
				break;
			}
		}
		lesson.find('.lesson-group').text(groupName);
		lesson.find('.lesson-course').text(courseName);
		lesson.find('.lesson-lector').text(lectorName);
		lesson.find('.lesson-auditory').text(auditoryName);
		lesson.find('.lesson-values').show(400);
		lesson.find('.lesson-add').attr('disabled', '').siblings().removeAttr('disabled');
	}
	
	function addLessonCancel(){
		group = null;
		course = null;
		lector = null;
		auditory = null;
		lesson = null;
		var modal = UIkit.modal('#modal-groups');
		if(modal.isActive())
		    modal.hide();
		modal = UIkit.modal('#modal-courses');
		if(modal.isActive())
		    modal.hide();
		modal = UIkit.modal('#modal-lectors');
		if(modal.isActive())
		    modal.hide();
		modal = UIkit.modal('#modal-auditories');
		if(modal.isActive())
		    modal.hide();
	}
	
	$(document).ready(function(){
		init();
		// add lesson click
		$('.lesson .lesson-add').click(function(event){
			group = null;
			course = null;
			lector = null;
			auditory = null;
			lesson = $(this).parents('.lesson').eq(0);
		    console.log("$('.lesson .lesson-add').click()");
		    console.log(lesson);
			add_lesson0(event);
		});
		$('#modal-groups .uk-modal-footer button').click(add_lesson1);
		$('#modal-courses .uk-modal-footer button').click(add_lesson2);
		$('#modal-lectors .uk-modal-footer button').click(add_lesson3);
		$('#modal-auditories .uk-modal-footer button').click(add_lesson4);
		// edit lesson click
		$('.lesson .lesson-edit').click(function(event){
		    lesson = $(this).parents('.lesson').eq(0);
		    group = lesson.data('group');
		    course = lesson.data('course');
		    lector = lesson.data('lector');
		    auditory = lesson.data('auditory');
		    add_lesson0(event);
		});
		// delete lesson click
		$('.lesson .lesson-delete').click(function(){
			lesson = $(this).parents('.lesson').eq(0);
			lesson.data('group', '').data('course', '').data('lector', '').data('audistory', '');
			lesson.find('.lesson-group, .lesson-course, .lesson-lector, .lesson-auditory').text('');
		    lesson.find('.lesson-values').hide(400);
			lesson.find('.lesson-add').removeAttr('disabled').siblings().attr('disabled', '');
		});
	});
	
	// END editing events
	
	
	
</script>

<form id="<?php echo $this->registry['controller_name']; ?>-form" class="uk-form" method="POST" action="/calendars/edit">
	<input type="hidden" name="action">
	<input type="hidden" name="events">
	<input type="hidden" name="id" value="<?php echo $calendar['id']; ?>">
	<div class="uk-form-row uk-margin-bottom">
		<input type="text" name="name" placeholder="Назва" value="<?php echo $calendar['name']; ?>">
		<h6 class="uk-text-muted uk-margin-remove">Не обов'язкове поле</h6>
	</div>	
	<div class="uk-form-row">
		<input type="radio" name="published" value="1" <?php if($calendar['published'] == '1') echo 'checked' ?>> Увімкнений<br>
		<input type="radio" name="published" value="0" <?php if($calendar['published'] == '0') echo 'checked' ?>> Вимкнений
	</div>
	<div class="uk-form-row">
        <?php
        if(empty($_GET['id'])){
            $checked = ($params->dual_week == '1') ? 'checked' : '';
        } else {
            $checked = ($calendar['dual_week'] == '1') ? 'checked' : '';
        }
        ?>
		<input type="checkbox" name="dual_week" <?php echo $checked; ?>> 2-х тижневий розклад
	</div>
	<div class="uk-form-row">
        <?php
        $timezones = array(
            'Europe/Amsterdam','Europe/Andorra','Europe/Astrakhan','Europe/Athens',
            'Europe/BelgradeEurope/Berlin','Europe/Bratislava','Europe/Brussels',
            'Europe/Bucharest','Europe/Budapest','Europe/Busingen','Europe/Chisinau',
            'Europe/Copenhagen','Europe/Dublin','Europe/Gibraltar','Europe/Guernsey',
            'Europe/Helsinki','Europe/Isle_of_Man','Europe/Istanbul','Europe/Jersey',
            'Europe/Kaliningrad','Europe/Kiev','Europe/Kirov','Europe/Lisbon',
            'Europe/Ljubljana','Europe/London','Europe/Luxembourg','Europe/Madrid',
            'Europe/Malta','Europe/Mariehamn','Europe/Minsk','Europe/Monaco',
            'Europe/Moscow','Europe/Oslo','Europe/Paris','Europe/Podgorica',
            'Europe/Prague','Europe/Riga','Europe/Rome','Europe/Samara',
            'Europe/San_Marino','Europe/Sarajevo','Europe/Simferopol','Europe/Skopje',
            'Europe/Sofia','Europe/Stockholm','Europe/Tallinn','Europe/Tirane',
            'Europe/Ulyanovsk','Europe/Uzhgorod','Europe/Vaduz','Europe/Vatican',
            'Europe/Vienna','Europe/Vilnius','Europe/Volgograd','Europe/Warsaw',
            'Europe/Zagreb','Europe/Zaporozhye','Europe/Zurich',
        );
        ?>
        <span>Часовий пояс </span>
		<select name="timezone">
            <?php
            foreach($timezones as $timezone){
                if($timezone == $calendar['timezone'])
                    $checked = 'selected="selected"';
                else
                    $checked = '';
                echo '<option value="' . $timezone . '" ' . $checked . '>' . $timezone . '</option>';
            }
            ?>
        </select>
	</div>
	<div class="uk-form-row">
		<button type="submit" type="button" data-uk-button class="btn-save btn-action uk-button uk-button-primary"><i class="uk-icon-save"></i> Зберегти</button>
	</div>
</form>

<script>
$(document).ready(function(){
	$("#<?php echo strtolower($this->registry['controller_name']); ?>-form [name='dual_week']").change(function(){
		if($(("#<?php echo strtolower($this->registry['controller_name']); ?>-form [name='dual_week']:checked")).val() == "on"){
			$('.weeks-tabs').show(400);
		} else {
			$('.weeks-tabs').hide(400).find('li').eq(0).click();
		}
	}).change();
	$('#<?php echo strtolower($this->registry['controller_name']); ?>-form').submit(function(event){
		var form = jQuery(this);
		var action = form.find('[name=action]');
		var btn = form.find('.btn-action.uk-active');
		if(btn.hasClass('btn-save'))
			action.val('save');
		// generate events for back-end
		var weeks = [];
		for(var i = 0; i < 2; i++){
			var week = [];
			
			for(var j = 0; j < 6; j++){
				var day = [];
				
				for(var k = 0; k < 9; k++){
					var lessonDom = $('.week').eq(i).find('.day').eq(j).find('.lesson').eq(k);
					var l = [lessonDom.data('group'), lessonDom.data('course'), lessonDom.data('lector'), lessonDom.data('auditory')];
					day.push(l);
				}
				
				week.push(day);
			}
			
			weeks.push(week);
		}
		if(!$("#<?php echo strtolower($this->registry['controller_name']); ?>-form [name='dual_week']:selected").length){
			weeks[1] = weeks[0];
		}
		$(this).find('[name=events]').val(JSON.stringify(weeks));
		
		//~ else
		//~ if(btn.hasClass('btn-close'))
			//~ action.val('close');
		//~ else
		//~ if(btn.hasClass('btn-new'))
			//~ action.val('new');
	});
});
</script>

<div id="modal-groups" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<div class="uk-modal-header">Виберіть групу</div>
        <table class="uk-table">
			<caption>Натисніть на рядок у таблиці нижче, а потім натисніть на кнопку "Вибрати"</caption>
			<thead>
				<tr>
					<th>Назва</th>
					<th>Інститут</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
        </table>
        <div class="uk-modal-footer uk-text-right">
            <button type="button" class="btn-cancel uk-button">Відміна</button>
            <button type="button" class="btn-save uk-button uk-button-primary">Вибрати та продовжити</button>
        </div>
	</div>
</div>

<div id="modal-courses" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<div class="uk-modal-header">Виберіть дисципліну</div>
        <table class="uk-table">
			<caption>Натисніть на рядок у таблиці нижче, а потім натисніть на кнопку "Вибрати"</caption>
			<thead>
				<tr>
					<th>Назва</th>
					<th>Опис</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
        </table>
        <div class="uk-modal-footer uk-text-right">
            <button type="button" class="btn-cancel uk-button">Відміна</button>
            <button type="button" class="btn-save uk-button uk-button-primary">Вибрати та продовжити</button>
        </div>
	</div>
</div>

<div id="modal-lectors" class="uk-modal">
	<div class="uk-modal-dialog uk-modal-dialog-large">
		<a class="uk-modal-close uk-close"></a>
		<div class="uk-modal-header">Виберіть викладача</div>
        <table class="uk-table">
			<thead>
				<tr>
					<th>Ім'я</th>
					<th>Призвіще</th>
					<th>По батькові</th>
					<th>Опис</th>
					<th>Інститут</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
        </table>
        <div class="uk-modal-footer uk-text-right">
            <button type="button" class="btn-cancel uk-button">Відміна</button>
            <button type="button" class="btn-save uk-button uk-button-primary">Вибрати та продовжити</button>
        </div>
	</div>
</div>

<div id="modal-auditories" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<div class="uk-modal-header">Виберіть аудиторію</div>
        <table class="uk-table">
			<thead>
				<tr>
					<th>Назва</th>
					<th>Інститут</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
        </table>
        <div class="uk-modal-footer uk-text-right">
            <button type="button" class="btn-cancel uk-button">Відміна</button>
            <button type="button" class="btn-save uk-button uk-button-primary">Вибрати та зберегти</button>
        </div>
	</div>
</div>

<div class="uk-margin">
	<div class="weeks-tabs uk-tab-center">
		<ul class="uk-tab" data-uk-tab data-uk-switcher="{connect:'.weeks'}">
			<li class="uk-active"><a href="">Чисельник</a></li>
			<li><a href="">Знаменник</a></li>
		</ul>
	</div>
	<ul class="weeks uk-margin-top uk-switcher">
	</ul>
</div>

