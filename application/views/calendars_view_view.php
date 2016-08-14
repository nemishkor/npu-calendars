<?php
$calendar = $data['calendar'];
$events = $calendar['events'];
$groups = $data['groups'];
$courses = $data['courses'];
$lectors = $data['lectors'];
$auditories = $data['auditories'];
?>

<style>
.weeks{
	list-style: none;
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
							</div>\
						<div class="lesson-values uk-width-1-1">\
							<div class="lesson-group uk-width-1-1" style="display:none"><i class="uk-icon-users"></i> <span></span></div>\
							<div class="lesson-course uk-width-1-1" style="display:none"><i class="uk-icon-book"></i> <span></span></div>\
							<div class="lesson-lector uk-width-1-1" style="display:none"><i class="uk-icon-mortar-board"></i> <span></span></div>\
							<div class="lesson-auditory uk-width-1-1" style="display:none"><i class="uk-icon-cube"></i> <span></span></div>\
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
	}
	
	// fill form data from filters
	function applyFilter(type, id){
		console.log("type = " + type + " id = " + id);
		if(type == "group"){
			filterType = "групою";
			filterName = getGroup(id)['name'];
		} else if(type == "course"){
			filterType = "дисципліною";
			filterName = getCourse(id)['name'];
		} else if(type == "lector"){
			filterType = "викладачем";
			filterName = getLector(id)['name'];
		} else if(type == "auditory"){
			filterType = "аудиторією";
			filterName = getAuditory(id)['name'];
		}
		$(".filter-header").text( "Розклад за: " + filterType + " - " + filterName);
		for (var i = 0; i < 2; i++){
			for (var j = 0; j < 6; j++){
				for (var k = 0; k < 9; k++){
					var lesson = $('.week').eq(i).find('.day').eq(j).find('.lesson').eq(k);
					if(type == "group" && events[i][j][k][0] == id || type == "course" && events[i][j][k][1] == id || type == "lector" && events[i][j][k][2] == id || type == "auditory" && events[i][j][k][3] == id){
						var group = getGroup(events[i][j][k][0]);
						var course = getCourse(events[i][j][k][1]);
						var lector = getLector(events[i][j][k][2]);
						var auditory = getAuditory(events[i][j][k][3]);
						if(type != 'group'){
							lesson.find('.lesson-group span').text(group['name']);
							lesson.find('.lesson-group').show(400);
						} else 
							lesson.find('.lesson-group').hide(400);
						if(type != 'course'){
							lesson.find('.lesson-course span').text(course['name']);
							lesson.find('.lesson-course').show(400);
						} else 
							lesson.find('.lesson-course').hide(400);
						if(type != 'lector'){
							lesson.find('.lesson-lector span').text(lector['name']);
							lesson.find('.lesson-lector').show(400);
						} else 
							lesson.find('.lesson-group').hide(400);
						if(type != 'auditory'){
							lesson.find('.lesson-auditory span').text(auditory['name']);
							lesson.find('.lesson-auditory').show(400);
						} else 
							lesson.find('.lesson-auditory').hide(400);
					} else {
						lesson.find('.lesson-group').hide();
						lesson.find('.lesson-group span').text('');
						lesson.find('.lesson-course').hide();
						lesson.find('.lesson-course span').text('');
						lesson.find('.lesson-lector').hide();
						lesson.find('.lesson-lector span').text('');
						lesson.find('.lesson-auditory').hide();
						lesson.find('.lesson-auditory span').text('');
					}
				}
			}
		}
		$('.weeks').fadeIn(400);
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
	
	$(document).ready(function(){
		init();
		// run filling form data with filters
		$('.groups-filter button').click(function(){
			applyFilter('group', $(this).data('id'));
		});
		$('.courses-filter button').click(function(){
			applyFilter('course', $(this).data('id'));
		});
		$('.lectors-filter button').click(function(){
			applyFilter('lector', $(this).data('id'));
		});
		$('.auditories-filter button').click(function(){
			applyFilter('auditory', $(this).data('id'));
		});
	});
</script>



<h1><i class="uk-icon-calendar"></i> Перегляд розкладу - <?php echo $calendar['name']; ?></h1>

<div class="uk-margin">
	<div class="uk-grid">
		<div class="uk-width-7-10" data-uk-button-radio>
			<p class="uk-alert"><i class="uk-close uk-close-alert"></i>Для перегляду розкладу виберіть фільтр. Наприклад, для пошуку викладача, виберіть фільтр по викладачам.</p>
			<div class="filters uk-accordion" data-uk-accordion>

				<h3 class="uk-accordion-title">Групи</h3>
				<div class="groups-filter uk-accordion-content">
					<?php
					foreach($groups as $group){
						echo '<button class="uk-button uk-button-large uk-margin-right" data-id="' . $group['id'] . '">' . $group['name'] . '</button>';
					}
					?>
				</div>

				<h3 class="uk-accordion-title">Дисципліни</h3>
				<div class="courses-filter uk-accordion-content">
					<?php
					foreach($courses as $course){
						echo '<button class="uk-button uk-button-large uk-margin-right" data-id="' . $courses['id'] . '">' . $course['name'] . '</button>';
					}
					?>
				</div>

				<h3 class="uk-accordion-title">Викладачі</h3>
				<div class="lectors-filter uk-accordion-content">
					<?php
					foreach($lectors as $lector){
						echo '<button class="uk-button uk-button-large uk-margin-right" data-id="' . $lector['id'] . '">' . $lector['name'] . '</button>';
					}
					?>
				</div>

				<h3 class="uk-accordion-title">Аудиторії</h3>
				<div class="auditories-filter uk-accordion-content">
					<?php
					foreach($auditories as $auditory){
						echo '<button class="uk-button uk-button-large uk-margin-right" data-id="' . $auditory['id'] . '">' . $auditory['name'] . '</button>';
					}
					?>
				</div>

			</div>
		</div>
		<div class="uk-width-3-10">
			<div class="uk-panel uk-panel-box uk-panel-box-secondary">
				<h3>Деталі</h3>
				<span>id: <?php echo $calendar['id']; ?></span><br>
				<?php $published = ($calendar['published']) ? '<span class="uk-text-success">Опублікований</span>' : '<span class="uk-text-warning">Не опублікований</span>'; echo $published; ?><br>
				<span>Дата створення: <?php echo $calendar['created']; ?></span><br>
				<span>Автор: <?php echo $calendar['created_by']; ?></span>
			</div>
		</div>
	</div>
	
	<h2 class="filter-header"></h2>
	
	<div class="weeks" style="display: none;"></div>
</div>
