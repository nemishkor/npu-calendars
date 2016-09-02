<h1><i class="uk-icon-google"></i>&nbsp;Календар <?php echo ($data['calendar']['name']) ? $data['calendar']['name'] : '[немає імені]'; ?></h1>
<div class="uk-grid">
    <div class="uk-width-1-1 uk-width-medium-1-2">
        <ul class="uk-list uk-list-line">
            <li class="uk-text-small uk-text-muted">
                <span class="uk-text-bold">id: </span>
                <?php echo $data['calendar']['id']; ?>
            </li>
            <li>
                <span class="uk-text-bold">Опублікований: </span>
                <?php echo ($data['calendar']['published']) ? '<span class="uk-text-success"><i class="uk-icon-check"></i></span>' : '<span class="uk-text-success"><i class="uk-icon-close"></i></span>'; ?>
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
                            foreach ($day as $lesson){
                                ?>
                                <td>
                                    <?php
                                    $exist = false;
                                    $valid = true;
                                    foreach ($lesson as $param){
                                        if(!is_null($param))
                                            $exist = true;
                                        else
                                            $valid = false;
                                    }
                                    if($valid){
                                        echo '<div class="uk-badge uk-badge-notification uk-badge-success">&nbsp;</div>';
                                    } else {
                                        if($exist)
                                            echo '<div class="uk-badge uk-badge-notification uk-badge-danger" title="Один з параметрів не вказано (викладач, група, дисципліна чи аудиторія)">&nbsp;</div>';
                                        else
                                            echo '<div class="uk-badge uk-badge-notification uk-badge-muted">&nbsp;</div>';
                                    }
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
    </div>
</div>

<div class="uk-modal" id="events-modal">
    <div class="uk-modal-dialog">
        <div class="uk-modal-header"></div>
        <a class="uk-close uk-modal-close"></a>
    </div>
</div>

<div class="uk-panel uk-margin">
    <h3 class="uk-panel-title">Розклад по групам: </h3>
    <?php
    if(!empty($data['groups'])):
    ?>
        <table class="uk-table">
            <tbody>
            <?php
            foreach ($data['groups'] as $group):
                ?>
                <tr>
                <td>
                    <?php
                    echo $group['name'];
                    ?>
                </td>
                <td>
                    <button class="uk-button uk-button-small">Переглянути розклад</button>
                </td>
                <td>
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
        <table class="uk-table">
            <tbody>
            <?php
            foreach ($data['courses'] as $course):
                ?>
                <tr>
                    <td>
                        <?php
                        echo $course['name'];
                        ?>
                    </td>
                    <td>
                        <button class="uk-button uk-button-small">Переглянути розклад</button>
                    </td>
                    <td>
                        <?php
                        $exist = false;
                        if(is_array($data['g_calendar_list_items']))
                            foreach($data['g_calendar_list_items'] as $g_item)
                                foreach($data['calendar']['g_calendars'] as $key => $saved_calendar)
                                    if($g_item['id'] = $saved_calendar && $key == 'course_' . $course['id'])
                                        $exist = true;
                        if($exist){
                            echo 'календар піключений. єааа<br>';
                            echo '<button class="uk-button uk-button-small" disabled title="Функція в процесі розробки" data-uk-tooltip>Синхронізувати</button>
                                              <button class="uk-button uk-button-small">Видалити</button>';
                        } else {
                            echo '<button class="uk-button uk-button-small">Додати до Google</button>';
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
        <table class="uk-table">
            <tbody>
            <?php
            foreach ($data['lectors'] as $lector):
                ?>
                <tr>
                    <td>
                        <?php
                        echo $lector['name'];
                        ?>
                    </td>
                    <td>
                        <button class="uk-button uk-button-small">Переглянути розклад</button>
                    </td>
                    <td>
                        <?php
                        $exist = false;
                        if(is_array($data['g_calendar_list_items']))
                            foreach($data['g_calendar_list_items'] as $g_item)
                                foreach($data['calendar']['g_calendars'] as $key => $saved_calendar)
                                    if($g_item['id'] = $saved_calendar && $key == 'lector_' . $lector['id'])
                                        $exist = true;
                        if($exist){
                            echo 'календар піключений. єааа<br>';
                            echo '<button class="uk-button uk-button-small" disabled title="Функція в процесі розробки" data-uk-tooltip>Синхронізувати</button>
                                              <button class="uk-button uk-button-small">Видалити</button>';
                        } else {
                            echo '<button class="uk-button uk-button-small">Додати до Google</button>';
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
        <table class="uk-table">
            <tbody>
            <?php
            foreach ($data['auditories'] as $auditory):
                ?>
                <tr>
                    <td>
                        <?php
                        echo $auditory['name'];
                        ?>
                    </td>
                    <td>
                        <button class="uk-button uk-button-small">Переглянути розклад</button>
                    </td>
                    <td>
                        <?php
                        $exist = false;
                        if(is_array($data['g_calendar_list_items']))
                            foreach($data['g_calendar_list_items'] as $g_item)
                                foreach($data['calendar']['g_calendars'] as $key => $saved_calendar)
                                    if($g_item['id'] = $saved_calendar && $key == 'auditory_' . $auditory['id'])
                                        $exist = true;
                        if($exist){
                            echo 'календар піключений. єааа<br>';
                            echo '<button class="uk-button uk-button-small" disabled title="Функція в процесі розробки" data-uk-tooltip>Синхронізувати</button>
                                              <button class="uk-button uk-button-small">Видалити</button>';
                        } else {
                            echo '<button class="uk-button uk-button-small">Додати до Google</button>';
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

<script>
    $(document).ready(function(){
        var clicks = 0;
        if($('h1 .uk-icon-google').length)
            $('h1 .uk-icon-google').click(function(){
                if(clicks == 7){
                    $obj = $('<style>#google-overlay,#google-overlay div{position:absolute;}</style>' +
                        '<div id="google-overlay" style="z-index:99999;top:150px;left:100px;">' +
                        '<div style="background-color:#F44336;border-radius:50%;"></div>' +
                        '<div style="background-color:#FFEB3B;border-radius:50%;"></div>' +
                        '<div style="background-color:#4CAF50;border-radius:50%;"></div>' +
                        '<div style="background-color:#2196F3;border-radius:50%;"></div>' +
                        '</div>');
                    $('html,body').css('overflow','hidden');
                    $('body').append($obj);
                    $('h1 .uk-icon-google').css('z-index','999999');
                    var width = ($(window).width() > $(window).height()) ? $(window).width() : $(window).height();
                    var offset = $('h1 .uk-icon-google').offset();
                    $('#google-overlay').css({'top':offset.top + 15,'left':offset.left + 15}).find('div').each(function(index, dom){
                        setTimeout(function(){
                            $(dom).animate({'margin' : width * (-1.5), 'width' : width * 3, 'height' : width * 3, 'opacity' : 0}, 1000);
                        }, index * 200);
                        setTimeout(function(){
                            $('#google-overlay').fadeOut();
                        },1600);
                    });
                }
                clicks++;
            });
    });
</script>