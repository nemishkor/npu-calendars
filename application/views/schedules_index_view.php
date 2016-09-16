<h1><i class="uk-icon-clock-o"></i>&nbsp;Виберіть групу розкладів</h1>
<div class="uk-panel-space">
    <div class="schedule-items uk-grid">
    <?php
    foreach ($data['calendars'] as $calendar){
        ?>
        <a href="/schedules/view?id=<?php echo $calendar['id']; ?>">
            <div class="uk-panel uk-panel-box">
                <h3 class="uk-panel-title uk-margin-bottom-remove"><?php echo $calendar['name']; ?></h3>
                <p class="uk-margin-top-remove">
                    Дата створення: <?php echo $calendar['created']; ?><br>
                    Початкова дата: <?php echo $calendar['start_date']; ?><br>
                    Кінцева дата: <?php echo $calendar['end_date']; ?><br>
                    Автор: <?php echo $calendar['user_name']; ?>
                </p>
            </div>
        </a>
        <?php
    }
    ?>
    </div>
</div>

<style>
    .schedule-items a{
        display: block;
        transition: .4s all;
    }
    .schedule-items a:hover{
        text-decoration: none;
        transform: scale(1.1);
    }
</style>