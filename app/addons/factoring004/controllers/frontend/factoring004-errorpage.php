<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/** @var \Tygh\SmartyEngine\Core $view */
$view = Tygh::$app['view'];

$view->display('design/themes/responsive/templates/factoring004-errorpage.tpl');
exit;