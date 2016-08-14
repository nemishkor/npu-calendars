<?php
require_once 'core/model.php';
require_once 'core/view.php';
require_once 'core/controller.php';
require_once 'core/crud_controller.php';
require_once 'core/registry.php';
require_once 'core/route.php';
require_once 'core/widget.php';
$registry = new Registry();
$router = new Route($registry);
?>
