<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

require_once DIR_ROOT . '/app/addons/factoring004/vendor/autoload.php';

/** @var $order_id */
/** @var $order_info */

$preApp = new \BnplPartners\Factoring004Payment\PreApp(
    $order_info['payment_method']['processor_params']['factoring004_api_host'],
    $order_info['payment_method']['processor_params']['factoring004_preapp_token'],
    isset($order_info['payment_method']['processor_params']['factoring004_debug_mode']),
);

fn_create_payment_form((string) $preApp->preApp($order_info), array());

exit;
