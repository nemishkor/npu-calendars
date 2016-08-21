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
	
	<link rel="stylesheet" href="/css/components/form-advanced.almost-flat.css">
	
	<script src="/js/components/accordion.js"></script>
	<link rel="stylesheet" href="/css/components/accordion.almost-flat.css">
	
	<script src="/js/components/notify.js"></script>
	<link rel="stylesheet" href="/css/components/notify.css">
	
	<script src="/js/components/tooltip.js"></script>
	<link rel="stylesheet" href="/css/components/tooltip.css">
	
	<script src="/js/core/toggle.js"></script>
	<script src="/js/core/switcher.js"></script>
	<script src="/js/core/tab.js"></script>
	
</head>
<body>
	<div class="uk-container uk-container-center">
		<nav class="uk-navbar uk-margin-top">

			<ul class="uk-navbar-nav">
				<li><a href="/auditories">Аудиторії</a></li>
				<li><a href="/calendars">Календарі</a></li>
				<li><a href="/courses">Дисципліни</a></li>
				<li><a href="/groups">Групи</a></li>
				<li><a href="/institutes">Інститути</a></li>
				<li><a href="/lectors">Викладачі</a></li>
				<li><a href="/settings">Налаштування</a></li>
				<li><a href="/doc">Документація</a></li>
			</ul>
			<div class="uk-float-right">
				<?php
//				$this->widget('login');
				?>
			</div>

		</nav>
	</div>
	<div class="uk-container uk-container-center uk-margin">
		
		<?php $this->widget('breadcrumbs'); ?>
		
		<?php
		if(!empty($this->registry['error']))
		echo '<p class="uk-alert uk-alert-danger" data-uk-alert><i class="uk-close uk-close-alert"></i><i class="uk-icon-exclamation-circle"></i> ' . $this->registry['error'] . '</p>';
		if(!empty($this->registry['info']))
		echo '<p class="uk-alert" data-uk-alert><i class="uk-close uk-close-alert"></i><i class="uk-icon-info"></i> ' . $this->registry['info'] . '</p>';
		?>
		
		<?php include 'application/views/'.$content_view; ?>

		<?php
		if(!empty($this->registry['debug'])){
			echo '<span class="uk-margin-large-top uk-text-muted">debug</span>';
			echo '<pre>';
			var_dump($this->registry['debug']);
			echo '</pre>';
		}
		if(!empty($data)){
		?>
			<span class="uk-margin-large-top uk-text-muted">var_dump($data);</span>
			<pre>
			<?php
			var_dump($data);
			?>
			</pre>
		<?php
		}
		?>
		
	</div>
	
</body>
</html>
