<h1><i class="uk-icon-clock-o"></i>&nbsp;Виберіть групу розкладів</h1>
<div class="uk-panel-space">
    <?php
    foreach ($data['calendars'] as $calendar){
        ?>
        <a href="/schedules/view?id=<?php echo $calendar['id']; ?>"
            <figure class="uk-overlay uk-width-1-1 uk-overlay-hover">
                <div style="background-image: url(/images/mh-1.jpg); height: 150px;"
                     class="uk-overlay-spin uk-width-1-1 uk-cover-background">
                </div>
                <div class="uk-position-cover uk-contrast"
                     style="background: -moz-linear-gradient(-45deg,  rgba(0,0,0,0) 0%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.4) 100%);
                            background: -webkit-linear-gradient(-45deg,  rgba(0,0,0,0) 0%,rgba(0,0,0,0) 50%,rgba(0,0,0,0.4) 100%);
                            background: linear-gradient(135deg,  rgba(0,0,0,0) 0%,rgba(0,0,0,0) 50%,rgba(0,0,0,0.4) 100%);
                            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00000000', endColorstr='#66000000',GradientType=1 );"
                    >
                    <div class="uk-width-1-10 uk-height-1-1 uk-overlay-background"
                         style="min-width: 250px; padding: 20px 0 0 20px;">
                        <h2><?php echo $calendar['name']; ?></h2>
                    </div>
                    <div class="uk-position-bottom-right uk-margin-right">
                        <i class="uk-icon-university" style="font-size: 86px;"></i>
                    </div>
                </div>
                <figcaption class="uk-overlay-panel uk-overlay-left uk-overlay-fade">
                    <h2 class="uk-invisible uk-margin-bottom-remove"><?php echo $calendar['name']; ?></h2>
                    <p class="uk-margin-top-remove">
                        Дата створення: <?php echo $calendar['created']; ?><br>
                        Початкова дата: <?php echo $calendar['start_date']; ?><br>
                        Кінцева дата: <?php echo $calendar['end_date']; ?><br>
                        Автор: <?php echo $calendar['user_name']; ?><br>
                    </p>
                </figcaption>
            </figure>
        </a>
        <?php
    }
    ?>
</div>
<style>
    .uk-overlay-hover:hover .uk-overlay-spin, .uk-overlay-hover.uk-hover .uk-overlay-spin, .uk-overlay-active .uk-active > .uk-overlay-spin {
        -webkit-transform: scale(1.2) rotate(1.6deg);
        transform: scale(1.2) rotate(1.6deg);
    }
</style>