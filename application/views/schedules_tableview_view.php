<?php
/**
 * Copyright (c) 2016. Vitaliy Korenev (http://newpage.in.ua)
 */

$calendar = $data['calendar'];
$events = $calendar['events'];
$groups = $data['groups'];
$courses = $data['courses'];
$lectors = $data['lectors'];
$auditories = $data['auditories'];
?>

<script>
    var auditories = <?php echo json_encode($data['auditories']); ?>;
    var courses = <?php echo json_encode($data['courses']); ?>;
    var groups = <?php echo json_encode($data['groups']); ?>;
    var institutes = <?php echo json_encode($data['institutes']); ?>;
    var lectors = <?php echo json_encode($data['lectors']); ?>;
    var events = <?php if($calendar) echo json_encode($calendar['events']); else echo 'null'; ?>;
    var schedules = <?php echo json_encode($calendar['g_calendars']); ?>;

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
	            .add('#reset-filter')
                .fadeIn(400);
        } else {
            $('.add-to-google-link, #reset-filter').fadeOut(400);
        }
        $(".filter-header").text( "Розклад за: " + filterType + " - " + filterName);
	    if(type == "group"){
			$('.table-schedule td[class*="group-' + id + '"], .table-schedule th[class*="group-' + id + '"]').removeClass('hidden-cell');
		    $('.table-schedule td:not([class*="group-' + id + '"], .day-name, .lesson-number, .lesson-time), .table-schedule th:not([class*="group-' + id + '"], .empty-cell)').addClass('hidden-cell');
	    } else {
		    if(type == "course") {
			    $('.table-schedule td[data-course!="' + id + '"]:not(.day-name, .lesson-number, .lesson-time)').addClass('hidden-cell');
			    $('.table-schedule td[data-course="' + id + '"]').removeClass('hidden-cell');
		    } else if(type == "lector") {
			    $('.table-schedule td[data-lector!="' + id + '"]:not(.day-name, .lesson-number, .lesson-time)').addClass('hidden-cell');
			    $('.table-schedule td[data-lector="' + id + '"]').removeClass('hidden-cell');
		    } else if(type == "auditory") {
			    $('.table-schedule td[data-auditory!="' + id + '"]:not(.day-name, .lesson-number, .lesson-time)').addClass('hidden-cell');
			    $('.table-schedule td[data-auditory="' + id + '"]').removeClass('hidden-cell');
		    }
	    }
        $('.weeks').fadeIn(400);
    }

    function getGroup(id){
        for(var j = 0; j < groups.length; j++){
            if(id == groups[j]['id']){
                return groups[j];
            }
        }
    }

    function getCourse(id){
        for(var j = 0; j < courses.length; j++){
            if(id == courses[j]['id']){
                return courses[j];
            }
        }
    }

    function getLector(id){
        for(var j = 0; j < lectors.length; j++){
            if(id == lectors[j]['id']){
                return lectors[j];
            }
        }
    }

    function getAuditory(id){
        for(var j = 0; j < auditories.length; j++){
            if(id == auditories[j]['id']){
                return auditories[j];
            }
        }
    }

    $(document).ready(function(){
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
	    $('#reset-filter').click(function(){
	    	$('.table-schedule td').removeClass('hidden-cell');
		    $(".filter-header").text('');
		    $(this).add('.add-to-google-link').fadeOut(400);
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
        <div class="uk-width-1-1 uk-width-medium-7-10">
            <p class="uk-alert"><i class="uk-close uk-close-alert"></i>Щоб додати розклад до свого Google календаря, виберіть фільтр</p>
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
                        echo '<button class="uk-button uk-button-large uk-margin-right" data-id="' . $course['id'] . '">' . $course['name'] . '</button>';
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
        <div class="uk-width-1-1 uk-width-medium-3-10">
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
	    <a id="reset-filter" class="uk-button uk-float-right uk-margin-right"
	       style="display: none;">Скинути фільтр</a>
    </div>

    <div class="weeks">
        <button id="table-colors-switcher" class="uk-button uk-position-absolute uk-position-top-left">
            <img src="/images/rgb.png"> <span class="hide-colors">Прибрати кольори</span><span class="show-colors">Кольоровий варіант</span>
        </button>
        <table class="table-schedule colored uk-table uk-text-center uk-table-condensed">
            <thead>
            <tr>
                <th class="empty-cell"></th>
                <th class="empty-cell"></th>
                <?php
                foreach ($groups as $group)
                    echo '<th class="group-' . $group['id'] . ' uk-text-center"><div data-uk-sticky>' . $group['name'] . '</div></th>';
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $day_names = ['Понеділок', 'Вівторок', 'Середа', 'Четвер', 'П\'ятниця', 'Субота'];
            foreach ($day_names as $day_number => $day_name){
                for($l = 0; $l < 9; $l++){
                    // find all lessons for the time interval (for example 8:00 - 9:20) for 2 weeks
                    $exs = array();
                    foreach ($groups as $group){
                        $first_week_lesson = null;
                        $ex = $events[0][$day_number][$l];
                        foreach ($ex as $lesson)
                            if ($lesson[0]['id'] == $group['id']){
                                $first_week_lesson = array(
                                    'group'     => $lesson[0],
                                    'course'    => $lesson[1],
                                    'lector'    => $lesson[2],
                                    'auditory'  => $lesson[3]
                                );
                            }
                        $second_week_lesson = null;
                        if($calendar['dual_week']){
                            $ex = $events[1][$day_number][$l];
                            foreach ($ex as $lesson)
                                if ($lesson[0]['id'] == $group['id'])
                                {
                                    $second_week_lesson = array(
                                        'group'     => $lesson[0],
                                        'course'    => $lesson[1],
                                        'lector'    => $lesson[2],
                                        'auditory'  => $lesson[3]
                                    );
//                                    $second_week_lesson = $lesson[1]['name'] . '<br>' . $lesson[2]['name'] . '<br>' . $lesson[3]['name'];
                                }
                        }
                        $exs[] = array($first_week_lesson, $second_week_lesson);
                    }
                    ?>
                    <tr class="day-<?php echo $day_number + 1; ?> uk-table-middle" data-lesson="<?php echo $l + 1; ?>">
                        <?php if($l == 0){ ?>
                            <td rowspan="18" class="day-name"><span><?php echo $day_name; ?></span></td>
                        <?php } ?>
                        <td class="lesson-number"><?php echo $l + 1; ?></td>
                        <?php
                        foreach ($groups as $group_key => $group){
                            echo '<td ' . ((!$exs[$group_key][1]) ? 'rowspan="2"' : '') . ' 
                                        data-group="' . $exs[$group_key][0]['group']['id'] . '" 
                                        data-course="' . $exs[$group_key][0]['course']['id'] . '"
                                        data-lector="' . $exs[$group_key][0]['lector']['id'] . '"
                                        data-auditory="' . $exs[$group_key][0]['auditory']['id'] . '"
                                        class="group-' . $group['id'] . '">';
                            echo $exs[$group_key][0]['course']['name'] . '<br>' . $exs[$group_key][0]['lector']['name'] . '<br>' . $exs[$group_key][0]['auditory']['name'];
                            echo '</td>';
                        }
                        ?>
                    </tr>
                    <tr class="day-<?php echo $day_number + 1; ?> uk-table-middle" data-lesson="<?php echo $l + 1; ?>">
                        <td class="lesson-time">
                            <?php
                            switch ($l) {
                                case 0:
                                    $start = '08:00';
                                    $end = '09:20';
                                    break;
                                case 1:
                                    $start = '09:30';
                                    $end = '10:50';
                                    break;
                                case 2:
                                    $start = '11:00';
                                    $end = '12:20';
                                    break;
                                case 3:
                                    $start = '12:30';
                                    $end = '13:50';
                                    break;
                                case 4:
                                    $start = '14:00';
                                    $end = '15:50';
                                    break;
                                case 5:
                                    $start = '16:00';
                                    $end = '17:20';
                                    break;
                                case 6:
                                    $start = '17:30';
                                    $end = '18:50';
                                    break;
                                case 7:
                                    $start = '19:00';
                                    $end = '20:20';
                                    break;
                                case 8:
                                    $start = '20:30';
                                    $end = '21:50';
                                    break;
                                default:
                                    $start = '00:00';
                                    $end = '00:00';
                                    break;
                            }
                            echo $start . '<br>-<br>' . $end;
                            ?>
                        </td>
                        <?php
                        foreach ($groups as $group_key => $group){
                            if($exs[$group_key][1]){
                                echo '<td 
                                        data-group="' . $exs[$group_key][1]['group']['id'] . '" 
                                        data-course="' . $exs[$group_key][1]['course']['id'] . '"
                                        data-lector="' . $exs[$group_key][1]['lector']['id'] . '"
                                        data-auditory="' . $exs[$group_key][1]['auditory']['id'] . '">';
                                echo $exs[$group_key][1]['course']['name'] . '<br>' . $exs[$group_key][1]['lector']['name'] . '<br>' . $exs[$group_key][1]['auditory']['name'];
                                echo '</td>';
                            }
                        }
                        ?>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
        <?php
        echo '<pre>';

        echo '</pre>';
        ?>
    </div>
</div>

<script>
    (function($) {
        $(function() {
            $('.table-schedule tr').hover(function(){
                $('tr[data-lesson="' + $(this).data("lesson") + '"]').addClass('hover');
            }, function(){
                $('tr[data-lesson="' + $(this).data("lesson") + '"]').removeClass('hover');
            });
            $('#table-colors-switcher').click(function(){
                $(this).toggleClass('active');
                $('.table-schedule').toggleClass('colored');
            });
        });
    })(jQuery);
</script>