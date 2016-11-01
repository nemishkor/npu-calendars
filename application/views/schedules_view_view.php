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
    var schedules = <?php echo json_encode($calendar['g_calendars']); ?>;
    // create events UI and fill data to them
    function init(){
        var dayNames = ['Понеділок', 'Вівторок', 'Середа', 'Четверг', 'П\'ятниця', 'Субота'];
        for (var i = 0; i < 2; i++){
            var week = $('<div class="week uk-grid uk-grid-small" data-uk-margin></div>');
            for (var j = 0; j < 6; j++){
                var day = $('<div class="day-wrapper uk-width-1-1 uk-width-small-1-3" data-uk-margin><div class="day uk-panel uk-panel-box"><span class="day-name uk-panel-title uk-muted uk-h3"></span><hr></div></div>');
                day.find('.day-name').text(dayNames[j]);
                for(var k = 0; k < 9; k++){
                    var lessons = $('\
					<div class="lessons uk-grid" data-uk-margin>\
                        <div class="lessons-header uk-width-1-1">\
                            <span class="lesson-number"></span>\
                        </div>\
					</div>');
                    for (var m = 0; m < events[i][j][k].length; m++){
                        var lesson = $($('.lesson-template').html());
                        lesson.find('.lesson-group span').text(getGroup(events[i][j][k][m][0])['name']);
                        lesson.find('.lesson-course span').text(getCourse(events[i][j][k][m][1])['name']);
                        lesson.find('.lesson-lector span').text(getLector(events[i][j][k][m][2])['name']);
                        lesson.find('.lesson-auditory span').text(getAuditory(events[i][j][k][m][3])['name']);
                        lessons.append(lesson[0].outerHTML).find('.lesson').last()
                            .data('group', events[i][j][k][m][0])
                            .data('course', events[i][j][k][m][1])
                            .data('lector', events[i][j][k][m][2])
                            .data('auditory', events[i][j][k][m][3]);
                    }
                    lessons.find('.lesson-number').text((k + 1) + ' - пара');
                    day.find('.day').append(lessons);
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
        if(schedules[type + '_' + id] != undefined){
            $('.add-to-google-link')
                .attr('href','https://calendar.google.com/calendar/render?cid=' + schedules[type + '_' + id])
                .fadeIn(400);
        } else {
            $('.add-to-google-link').fadeOut(400);
        }
        $(".filter-header").text( "Розклад за: " + filterType + " - " + filterName);
        for (var i = 0; i < 2; i++){
            for (var j = 0; j < 6; j++){
                for (var k = 0; k < 9; k++){
                    var lessons = $('.week').eq(i).find('.day').eq(j).find('.lessons').eq(k);
                    for (var m = 0; m < lessons.length; m++) {
                        var lesson = lessons.find('.lesson').eq(m);
                        console.log('m = ' + m);
                        console.log("lesson.data('group') = " + lesson.data('group'));
                        console.log("id = " + id);
                        if (type == "group" && lesson.data('group') == id ||
                            type == "course" && lesson.data('course') == id ||
                            type == "lector" && lesson.data('lector') == id ||
                            type == "auditory" && lesson.data('group') == id) {
                            console.log('show lesson');
                            lesson.show();
                            if (type != 'group') {
                                lesson.find('.lesson-group').show(400);
                            } else
                                lesson.find('.lesson-group').hide(400);
                            if (type != 'course') {
                                lesson.find('.lesson-course').show(400);
                            } else
                                lesson.find('.lesson-course').hide(400);
                            if (type != 'lector') {
                                lesson.find('.lesson-lector').show(400);
                            } else
                                lesson.find('.lesson-lector').hide(400);
                            if (type != 'auditory') {
                                lesson.find('.lesson-auditory').show(400);
                            } else
                                lesson.find('.lesson-auditory').hide(400);
                        } else {
                            lesson.hide();
                        }
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


<div class="lesson-template">
    <div class="lesson uk-width-1-1" style="display: none;">
        <div class="lesson-group uk-width-1-1"><i class="uk-icon-users"></i> <span></span></div>
        <div class="lesson-course uk-width-1-1"><i class="uk-icon-book"></i> <span></span></div>
        <div class="lesson-lector uk-width-1-1"><i class="uk-icon-mortar-board"></i> <span></span></div>
        <div class="lesson-auditory uk-width-1-1"><i class="uk-icon-cube"></i> <span></span></div>
    </div>
</div>


<h1><i class="uk-icon-clock-o"></i> <?php echo $calendar['name']; ?></h1>

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
                Дата створення: <?php echo $calendar['created']; ?><br>
                Початкова дата: <?php echo $calendar['start_date']; ?><br>
                Кінцева дата: <?php echo $calendar['end_date']; ?><br>
                Автор: <?php echo $calendar['user_name']; ?><br>
            </div>
        </div>
    </div>

    <div class="uk-margin-top uk-margin-bottom">
        <span class="uk-h2 filter-header"></span>
        <a class="add-to-google-link uk-float-right uk-button uk-button-primary"
           href="#"
           target="_blank"
           rel="nofollow"
           style="display: none;">Додати до свого календаря Google</a>
    </div>

    <div class="weeks" style="display: none;"></div>
</div>