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

    $(document).ready(function(){
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
    <div class="uk-panel uk-panel-box uk-panel-box-secondary">
        <h3>Деталі</h3>
        Дата створення: <?php echo $calendar['created']; ?><br>
        Початкова дата: <?php echo $calendar['start_date']; ?><br>
        Кінцева дата: <?php echo $calendar['end_date']; ?><br>
        Автор: <?php echo $calendar['user_name']; ?><br>
    </div>

    <div class="uk-margin-top uk-margin-bottom">
        <span class="uk-h2 filter-header"></span>
        <a class="add-to-google-link uk-float-right uk-button uk-button-primary"
           href="#"
           target="_blank"
           rel="nofollow"
           style="display: none;">Додати до свого календаря Google</a>
    </div>

    <div class="weeks uk-position-relative">
        <button id="table-colors-switcher" class="uk-button uk-position-absolute uk-position-top-left">
            <img src="/images/rgb.png"> <span class="hide-colors">Прибрати кольори</span><span class="show-colors">Кольоровий варіант</span>
        </button>
        <table class="table-schedule colored uk-table uk-text-center uk-table-condensed">
            <thead>
            <tr>
                <th></th>
                <th></th>
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
                                $first_week_lesson = $lesson[1]['name'] . '<br>' . $lesson[2]['name'] . '<br>' . $lesson[3]['name'];
                            }
                        $second_week_lesson = null;
                        if($calendar['dual_week']){
                            $ex = $events[1][$day_number][$l];
                            foreach ($ex as $lesson)
                                if ($lesson[0]['id'] == $group['id'])
                                {
                                    $second_week_lesson = $lesson[1]['name'] . '<br>' . $lesson[2]['name'] . '<br>' . $lesson[3]['name'];
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
                            echo '<td ' . ((!$exs[$group_key][1]) ? 'rowspan="2"' : '') . '>';
                            echo $exs[$group_key][0];
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
                                echo '<td>';
                                echo $exs[$group_key][1];
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