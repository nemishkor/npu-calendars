<div class="uk-grid">
    <div class="uk-width-1-1 uk-width-medium-1-2">
        <ul class="uk-list uk-list-line">
            <li class="uk-text-small uk-text-muted">
                <span class="uk-text-bold">id: </span>
                <?php echo $data['calendar']['id']; ?>
            </li>
            <li>
                <span class="uk-text-bold">Назва: </span>
                <?php echo ($data['calendar']['name']) ? $data['calendar']['name'] : '[немає імені]'; ?>
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
            if(empty($data['calendar']['dual_week'])){
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
        <p>Даний календар не збережений до ваших Google календарів.</p>
        <p>Ви можете додати цей календар до ваших Google календарів, щоб обмінюватися ними з іншими користувачами, переглядати та редагувати його на вашому смартфоні чи на <a href="http://calendar.google.com/">www.calendar.google.com</a></p>
    </div>
</div>
<form class="uk-margin" action="/calendars/add_to_google">
    <input type="hidden" name="task" value="add">
    <input type="hidden" name="id" value="<?php echo $data['calendar']['id']; ?>">
    <button class="uk-button uk-button-success">Додати цей календар до Google</button>
</form>