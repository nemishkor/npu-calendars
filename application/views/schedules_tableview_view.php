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
    // create events UI and fill data to them

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
        init();

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

    <div class="weeks">
        <?php
        for($i = 0; $i <= $calendar['dual_week']; $i++){
            ?>
            <table class="uk-table">
                <thead>
                <th>
                    <td></td>
                    <?php
                    foreach ($groups as $group)
                        echo '<td class="group-' . $group['id'] . '">' . $group['name'] . '</td>';
                    ?>
                </th>
                </thead>
                <tbody>
                <?php
                $day_names = ['Понеділок', 'Вівторок', 'Середа', 'Четвер', 'П\'ятниця', 'Субота'];
                foreach ($day_names as $day_number => $day_name){
                    for($l = 0; $l < 9; $l++){
                        ?>
                        <tr>
                            <?php if($l == 0){ ?>
                                <td rowspan="18"><?php echo $day_name; ?></td>
                            <?php } ?>
                            <td><?php echo $l + 1; ?></td>
                            <?php
                            foreach ($groups as $group){
                                echo '<td>';
                                $day = $events[0][$day_number];
                                foreach ($day as $ex)
                                    foreach ($ex as $lesson){
                                        if($lesson[0] == $group['id']){
                                            ?>
                                            дисципліна - <?php echo $lesson ?>
                                            <?php
                                        }
                                    }
                                echo '</td>';
                            } // groups
                            ?>
                        </tr>
                        <tr>
                            <td>
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
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
            <?php
        }
        echo '<pre>';
        var_dump($calendar);
        echo '</pre>';
        ?>
    </div>
</div>