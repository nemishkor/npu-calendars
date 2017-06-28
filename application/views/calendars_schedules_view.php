<h1><i class="uk-icon-google"></i>&nbsp;Розклади <?php echo ($data['calendar']['name']) ? $data['calendar']['name'] : '[немає імені]'; ?></h1>
<div class="uk-grid">
    <div class="uk-width-1-1 uk-width-medium-1-2">
        <ul class="uk-list uk-list-line">
            <li class="uk-text-small uk-text-muted">
                <span class="uk-text-bold">id: </span>
                <?php echo $data['calendar']['id']; ?>
            </li>
            <li>
                <span class="uk-text-bold">Опублікований: </span>
                <?php
                if($data['calendar']['published'])
                    echo '<span class="uk-text-success"><i class="uk-icon-check"></i></span>&nbsp;
                          <span class="uk-text-success">Студентам (учням) будуть доступні для перегляду ваші додані в Google розклади</span>';
                else
                    echo '<span class="uk-text-success"><i class="uk-icon-close"></i></span>&nbsp;
                          <span>Студенти (учні) не будуть бачити ваші розклади</span>'; ?>
            </li>
            <li>
                <span class="uk-text-bold">Дата створення: </span>
                <?php echo ($data['calendar']['created']); ?>
            </li>
            <li>
                <span class="uk-text-bold">Часовий пояс: </span>
                <?php echo ($data['calendar']['timezone']); ?>
            </li>
        </ul>
        <div class="uk-panel uk-panel-box">
            <h3 class="uk-panel-title">Події</h3>
            <?php
            $events = $data['calendar']['events'];
            echo '<span class="uk-text-bold">2-х тижневий розклад: </span>';
            if($data['calendar']['dual_week'] == '1'){
                echo '<span class="uk-text-success"><i class="uk-icon-check"></i></span><br>';
            } else {
                echo '<span><i class="uk-icon-close"></i></span><br>';
            }
            foreach ($events as $week_index=>$week) {
                if(!empty($data['calendar']['dual_week'])){
                    echo '<span>Тиждень №' . $week_index + 1 . '></span>';
                }
                if(empty($data['calendar']['dual_week']) && $week_index == 1)
                    break;
                ?>
                <table class="uk-table uk-table-condensed">
                    <thead>
                    <tr class="uk-table-middle">
                        <th rowspan="2">День</th>
                        <th colspan="9" class="uk-text-center">№ заняття</th>
                    </tr>
                    <tr>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                        <th>5</th>
                        <th>6</th>
                        <th>7</th>
                        <th>8</th>
                        <th>9</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($week as $day_index=>$day){
                        ?>
                        <tr>
                            <td>
                                <?php
                                switch ($day_index){
                                    case 0: echo 'Пн'; break;
                                    case 1: echo 'Вт'; break;
                                    case 2: echo 'Ср'; break;
                                    case 3: echo 'Чт'; break;
                                    case 4: echo 'Пт'; break;
                                    case 5: echo 'Сб'; break;
                                }
                                ?>
                            </td>
                            <?php
                            foreach ($day as $lessons){
                                ?>
                                <td>
                                    <?php
                                    $valid = true;
                                    foreach ($lessons as $lesson) {
                                        foreach ($lesson as $param) {
                                            if (is_null($param))
                                                $valid = false;
                                        }
                                    }
                                    if(count($lessons)){
                                        if($valid)
                                            echo '<div class="uk-badge uk-badge-notification uk-badge-success">' .
                                             count($lessons) .
                                             '</div>';
                                        else
                                            echo '<div class="uk-badge uk-badge-notification uk-badge-danger" title="Один з параметрів не вказано (викладач, група, дисципліна чи аудиторія)">&nbsp;</div>';
                                    } else
                                        echo '<div class="uk-badge uk-badge-notification uk-badge-muted">&nbsp;</div>';
                                    ?>
                                </td>
                                <?php
                            }
                            ?>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </div>
    </div>
    <div class="uk-width-1-1 uk-width-medium-1-2">
        <p>Тут ви можете додати розклади до Google календарів та керувати ними.</p>
        <p>Ви можете обмінюватися ними з іншими користувачами, переглядати та редагувати його на вашому смартфоні чи на <a href="http://calendar.google.com/">www.calendar.google.com</a></p>
        <p>Студенти (учні, інші користувачі) зможуть бачити ваші розклади з Google лише, якщо даний календар опублікований</p>
    </div>
</div>

<div class="uk-grid">
    <?php
    $all_link = '/calendars/schedules?id=' . $data['calendar']['id'];
    $filters_base = array('groups','courses','lectors','auditories');
    foreach ($filters_base as $filter_base) {
        if (count($data[$filter_base]))
            $all_link .= '&' . $filter_base . '=';
        foreach ($data[$filter_base] as $index => $filter) {
            $all_link .= $filter['id'];
            if (count($data[$filter_base]) - 1 != $index)
                $all_link .= '-';
        }
    }
    ?>
    <div class="uk-width-1-4">
        <a href="<?php echo $all_link . '&task=add'; ?>"
           class="uk-button uk-button-success uk-width-1-1 uk-button-large">Додати всі розклади</a>
    </div>
    <div class="uk-width-1-4">
        <a href="<?php echo $all_link . '&task=delete'; ?>"
           class="uk-button uk-button-danger uk-width-1-1 uk-button-large">Видалити всі розклади</a>
    </div>
    <div class="uk-width-1-2">
        <div class="uk-alert">
            <i class="uk-icon-info-circle"></i> Ви можете додати/видалити всі розклади відразу, або вибірково нижче
        </div>
    </div>
</div>

<div class="uk-panel uk-margin uk-form">
    <h3 class="uk-panel-title">Розклад по групам: </h3>
    <?php
    if(!empty($data['groups'])):
    ?>
        <table class="schedules-groups uk-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Назва</th>
                    <th>Дії</th>
                    <th>id</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($data['groups'] as $group):
                ?>
                <tr>
                    <td><input type="checkbox" data-value="<?php echo $group['id']; ?>"></td>
                    <td>
                        <?php
                        echo $group['name'];
                        ?>
                    </td>
                    <td>
                        <button class="uk-button uk-button-small" disabled title="Функція в процесі розробки" data-uk-tooltip>Переглянути розклад</button>
                        <?php
                        $link = "/calendars/schedules?id=" .
                            $data['calendar']['id'] .
                            "&groups=" .
                            $group['id'];
                        $exist = false;
                        if(is_array($data['g_calendar_list_items']))
                            foreach($data['g_calendar_list_items'] as $g_item)
                                foreach($data['calendar']['g_calendars'] as $key => $saved_calendar)
                                    if($g_item['id'] = $saved_calendar && $key == 'group_' . $group['id'])
                                        $exist = true;
                        if($exist){
                            echo 'календар піключений. єааа<br>';
                            echo '<button class="uk-button uk-button-small" disabled title="Функція в процесі розробки" data-uk-tooltip>Синхронізувати</button>
                                  <a href="' . $link . '&task=delete" class="uk-button uk-button-small">Видалити</a>';
                        } else {
                            echo '<a href="' . $link . '&task=add" class="uk-button uk-button-small">Додати до Google</a>';
                        }
                        ?>
                    </td>
                    <td class="uk-text-small uk-text-muted">
                        <?php
                        echo $group['id'];
                        ?>
                    </td>
                </tr>
                <?php
            endforeach;
            ?>
            </tbody>
        </table>
    <?php
    endif;
    ?>
    <h3 class="uk-panel-title">Розклад по дисциплінам:</h3>
    <?php
    if(!empty($data['courses'])):
        ?>
        <table class="schedules-courses uk-table">
            <thead>
            <tr>
                <th></th>
                <th>Назва</th>
                <th>Дії</th>
                <th>id</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($data['courses'] as $course):
                ?>
                <tr>
                    <td><input type="checkbox" data-value="<?php echo $course['id']; ?>"></td>
                    <td>
                        <?php
                        echo $course['name'];
                        ?>
                    </td>
                    <td>
                        <button class="uk-button uk-button-small">Переглянути розклад</button>
                        <?php
                        $link = "/calendars/schedules?id=" .
                            $data['calendar']['id'] .
                            "&courses=" .
                            $course['id'];
                        $exist = false;
                        if(is_array($data['g_calendar_list_items']))
                            foreach($data['g_calendar_list_items'] as $g_item)
                                foreach($data['calendar']['g_calendars'] as $key => $saved_calendar)
                                    if($g_item['id'] = $saved_calendar && $key == 'course_' . $course['id'])
                                        $exist = true;
                        if($exist){
                            echo 'календар піключений. єааа<br>';
                            echo '<button class="uk-button uk-button-small" disabled title="Функція в процесі розробки" data-uk-tooltip>Синхронізувати</button>
                                  <a href="' . $link . '&task=delete" class="uk-button uk-button-small">Видалити</a>';
                        } else {
                            echo '<a href="' . $link . '&task=add" class="uk-button uk-button-small">Додати до Google</a>';
                        }
                        ?>
                    </td>
                    <td class="uk-text-small uk-text-muted">
                        <?php
                        echo $course['id'];
                        ?>
                    </td>
                </tr>
                <?php
            endforeach;
            ?>
            </tbody>
        </table>
        <?php
    endif;
    ?>
    <h3 class="uk-panel-title">Розклад по викладач:</h3>
    <?php
    if(!empty($data['lectors'])):
        ?>
        <table class="schedules-lectors uk-table">
            <thead>
            <tr>
                <th></th>
                <th>Назва</th>
                <th>Дії</th>
                <th>id</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($data['lectors'] as $lector):
                ?>
                <tr>
                    <td><input type="checkbox" data-value="<?php echo $lector['id']; ?>"></td>
                    <td>
                        <?php
                        echo $lector['name'];
                        ?>
                    </td>
                    <td>
                        <button class="uk-button uk-button-small">Переглянути розклад</button>
                        <?php
                        $link = "/calendars/schedules?id=" .
                            $data['calendar']['id'] .
                            "&lectors=" .
                            $lector['id'];
                        $exist = false;
                        if(is_array($data['g_calendar_list_items']))
                            foreach($data['g_calendar_list_items'] as $g_item)
                                foreach($data['calendar']['g_calendars'] as $key => $saved_calendar)
                                    if($g_item['id'] = $saved_calendar && $key == 'lector_' . $lector['id'])
                                        $exist = true;
                        if($exist){
                            echo 'календар піключений. єааа<br>';
                            echo '<button class="uk-button uk-button-small" disabled title="Функція в процесі розробки" data-uk-tooltip>Синхронізувати</button>
                                  <a href="' . $link . '&task=delete" class="uk-button uk-button-small">Видалити</a>';
                        } else {
                            echo '<a href="' . $link . '&task=add" class="uk-button uk-button-small">Додати до Google</a>';
                        }
                        ?>
                    </td>
                    <td class="uk-text-small uk-text-muted">
                        <?php
                        echo $lector['id'];
                        ?>
                    </td>
                </tr>
                <?php
            endforeach;
            ?>
            </tbody>
        </table>
        <?php
    endif;
    ?>
    <h3 class="uk-panel-title">Розклад по аудиторіям:</h3>
    <?php
    if(!empty($data['auditories'])):
        ?>
        <table class="schedules-auditories uk-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Назва</th>
                    <th>Дії</th>
                    <th>id</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($data['auditories'] as $auditory):
                ?>
                <tr>
                    <td><input type="checkbox" data-value="<?php echo $auditory['id']; ?>"></td>
                    <td>
                        <?php
                        echo $auditory['name'];
                        ?>
                    </td>
                    <td>
                        <button class="uk-button uk-button-small">Переглянути розклад</button>
                        <?php
                        $link = "/calendars/schedules?id=" .
                            $data['calendar']['id'] .
                            "&auditories=" .
                            $auditory['id'];
                        $exist = false;
                        if(is_array($data['g_calendar_list_items']))
                            foreach($data['g_calendar_list_items'] as $g_item)
                                foreach($data['calendar']['g_calendars'] as $key => $saved_calendar)
                                    if($g_item['id'] = $saved_calendar && $key == 'auditory_' . $auditory['id'])
                                        $exist = true;
                        if($exist){
                            echo 'календар піключений. єааа<br>';
                            echo '<button class="uk-button uk-button-small" disabled title="Функція в процесі розробки" data-uk-tooltip>Синхронізувати</button>
                                  <a href="' . $link . '&task=delete" class="uk-button uk-button-small">Видалити</a>';
                        } else {
                            echo '<a href="' . $link . '&task=add" class="uk-button uk-button-small">Додати до Google</a>';
                        }
                        ?>
                    </td>
                    <td class="uk-text-small uk-text-muted">
                        <?php
                        echo $auditory['id'];
                        ?>
                    </td>
                </tr>
                <?php
            endforeach;
            ?>
            </tbody>
        </table>
        <?php
    endif;
    ?>
</div>

<form class="selection-actions" style="display: none;" action="/calendars/schedules">
    <input type="hidden" name="id" value="<?php echo $data['calendar']['id']; ?>">
    <input type="hidden" name="groups">
    <input type="hidden" name="courses">
    <input type="hidden" name="lectors">
    <input type="hidden" name="auditories">
    <input type="hidden" name="task">
    <button class="add-btn uk-button uk-button-success">Додати вибрані розклади</button>
    <button class="delete-btn uk-button uk-button-danger">Видалити вибрані розклади</button>
</form>
<script>
    $(document).ready(function(){
        $('table[class*="schedules-"] input').change(function(){
            if(jQuery("table[class*='schedules-'] :checked").length)
                $('form.selection-actions').show(400);
            else
                $('form.selection-actions').hide(400);
        });
        $('form.selection-actions button').on('click', function(event){
            var form = $(this).parents('form').first();
            var check = jQuery("table[class*='schedules-'] :checked");
            if(check.length){
                var filters = ['groups', 'courses', 'lectors', 'auditories'];
                for(var i = 0; i < filters.length; i++){
                    form.find('[name="' + filters[i] + '"]').val();
                    $('.schedules-' + filters[i] + ' input:checked').each(function(index, dom){
                        var value = form.find('[name="' + filters[i] + '"]').val();
                        if(index != 0)
                            value += '-';
                        form.find('[name="' + filters[i] + '"]').val(value + $(dom).attr('data-value'));
                    });
                }
                if($(this).hasClass('add-btn'))
                    form.find('[name="task"]').val('add');
                else if($(this).hasClass('delete-btn'))
                    form.find('[name="task"]').val('delete');
            } else {
                UIkit.notify("Відмітьте елемент вище", {status:"warning"});
                event.preventDefault();
            }
        });
    });
</script>

<div class="uk-modal" id="events-modal">
    <div class="uk-modal-dialog">
        <div class="uk-modal-header"></div>
        <a class="uk-close uk-modal-close"></a>
    </div>
</div>

<?php
echo base64_decode('PHNjcmlwdD4kKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigpe3ZhciBhPTA7JCgiaDEgLnVrLWljb24tZ29vZ2xlIikubGVuZ3RoJiYkKCJoMSAudWstaWNvbi1nb29nbGUiKS5jbGljayhmdW5jdGlvbigpe2lmKDY9PWEpeyRvYmo9JCgnPHN0eWxlPiNnMzc0NjMsI2czNzQ2MyBkaXZ7cG9zaXRpb246YWJzb2x1dGU7ei1pbmRleDo5OTk5OTt9I2czNzQ2MyBkaXZ7Ym9yZGVyLXJhZGl1czo1MCU7fTwvc3R5bGU+PGRpdiBpZD0iZzM3NDYzIj48ZGl2IHN0eWxlPSJiYWNrZ3JvdW5kLWNvbG9yOiNGNDQzMzY7Ij48L2Rpdj48ZGl2IHN0eWxlPSJiYWNrZ3JvdW5kLWNvbG9yOiNGRkVCM0I7Ij48L2Rpdj48ZGl2IHN0eWxlPSJiYWNrZ3JvdW5kLWNvbG9yOiM0Q0FGNTA7Ij48L2Rpdj48ZGl2IHN0eWxlPSJiYWNrZ3JvdW5kLWNvbG9yOiMyMTk2RjM7Ij48L2Rpdj48L2Rpdj4nKSwkKCJodG1sLGJvZHkiKS5jc3MoIm92ZXJmbG93LXgiLCJoaWRkZW4iKSwkKCJib2R5IikuYXBwZW5kKCRvYmopLCQoImgxIC51ay1pY29uLWdvb2dsZSIpLmNzcyh7InotaW5kZXgiOiI5OTk5OTkiLCJwb3NpdGlvbiI6InJlbGF0aXZlIn0pO3ZhciBiPSQod2luZG93KS53aWR0aCgpPiQod2luZG93KS5oZWlnaHQoKT8kKHdpbmRvdykud2lkdGgoKTokKHdpbmRvdykuaGVpZ2h0KCksYz0kKCJoMSAudWstaWNvbi1nb29nbGUiKS5vZmZzZXQoKTskKCIjZzM3NDYzIikuY3NzKHt0b3A6Yy50b3ArMTUsbGVmdDpjLmxlZnQrMTV9KS5maW5kKCJkaXYiKS5lYWNoKGZ1bmN0aW9uKGEsYyl7c2V0VGltZW91dChmdW5jdGlvbigpeyQoYykuYW5pbWF0ZSh7bWFyZ2luOmIqLTEuNSx3aWR0aDozKmIsaGVpZ2h0OjMqYixvcGFjaXR5OjB9LDFlMyl9LDIwMCphKSxzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7JCgiI2czNzQ2MyIpLmZhZGVPdXQoNDAwLGZ1bmN0aW9uKCl7JCgiI2czNzQ2MyIpLnJlbW92ZSgpfSl9LDE2MDApfSl9YSsrfSl9KTs8L3NjcmlwdD4=');
?>
