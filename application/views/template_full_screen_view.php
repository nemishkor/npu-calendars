<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title></title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script src="/js/uikit.js"></script>
    <link rel="stylesheet" href="/css/uikit.almost-flat.css">
    <link rel="stylesheet" href="/css/style.css">

    <link rel="stylesheet" href="/css/components/form-advanced.almost-flat.css">

    <script src="/js/components/accordion.js"></script>
    <link rel="stylesheet" href="/css/components/accordion.almost-flat.css">

    <script src="/js/components/notify.js"></script>
    <link rel="stylesheet" href="/css/components/notify.css">

    <script src="/js/components/tooltip.min.js"></script>
    <link rel="stylesheet" href="/css/components/tooltip.css">

    <script src="/js/core/toggle.js"></script>
    <script src="/js/core/switcher.js"></script>
    <script src="/js/core/tab.js"></script>

</head>
<body>
<div class="uk-margin">

    <?php
    if(!empty($this->registry['error']) || !empty($this->registry['info'])):
    ?>
    <div class="uk-position-absolute uk-width-1-1 uk-position-top-left uk-margin-large-top">
        <div class="uk-width-1-3 uk-container-center">
            <?php
            if(!empty($this->registry['error']))
                echo '<p class="uk-alert uk-alert-danger" data-uk-alert><i class="uk-close uk-close-alert"></i><i class="uk-icon-exclamation-circle"></i> ' . $this->registry['error'] . '</p>';
            if(!empty($this->registry['info']))
                echo '<p class="uk-alert" data-uk-alert><i class="uk-close uk-close-alert"></i><i class="uk-icon-info"></i> ' . $this->registry['info'] . '</p>';
            ?>
        </div>
    </div>
    <?php
    endif;
    ?>

    <?php include 'application/views/'.$content_view; ?>

</div>

</body>
</html>
