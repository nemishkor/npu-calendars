<h1><i class="uk-icon-clock-o"></i>&nbsp;Виберіть навчальний заклад або підприємство</h1>
<form class="uk-form">
    <label>Або знайдіть </label>
    <input name="s" type="text" class="uk-form-width-large" placeholder="Назва закладу або підприємства">
    <input type="submit" class="uk-button" value="Знайти">
</form>
<div class="uk-panel-space">
    <div class="schedule-items uk-grid">
    <?php
    foreach ($data['calendars'] as $calendar){
        ?>
        <div class="schedule-panel uk-panel uk-panel-box">
            <h3 class="uk-panel-title uk-margin-bottom-remove"><?php echo $calendar['name']; ?></h3>
            <p class="uk-margin-top-remove">
                Дата створення: <?php echo $calendar['created']; ?><br>
                Початкова дата: <?php echo $calendar['start_date']; ?><br>
                Кінцева дата: <?php echo $calendar['end_date']; ?><br>
                Навчальний заклад: <?php echo $calendar['organization']; ?><br>
                Автор: <?php echo $calendar['user_name']; ?>
            </p>
            <div class="uk-text-center">
                Відкрити розклад у вигляді<br>
                <div class="uk-button-group">
                    <a href="/schedules/table_view?id=<?php echo $calendar['id']; ?>" class="uk-button"><i class="uk-icon-table"></i> таблиці</a>
                    <a href="/schedules/block_view?id=<?php echo $calendar['id']; ?>" class="uk-button"><i class="uk-icon-th"></i> блоків</a>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    </div>
</div>

<style>
    .schedule-panel{
        display: block;
        transition: .4s all;
    }
    .schedule-panel:hover{
        text-decoration: none;
        transform: scale(1.1);
    }
</style>