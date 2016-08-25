<?php
if(empty($data['calendar']['g_calendar_id'])){
    $title = "Додати до Google";
} else {
    $title = "Синхронізувати з Google";
}
?>
<h1><i class="uk-icon-google"></i>&nbsp;<?php echo $title; ?></h1>
