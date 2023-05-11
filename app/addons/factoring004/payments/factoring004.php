<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

require_once DIR_ROOT . '/app/addons/factoring004/vendor/autoload.php';

/** @var $order_id */
/** @var $order_info */
try {
    $preApp = new \BnplPartners\Factoring004Payment\PreApp(
        $order_info['payment_method']['processor_params']['factoring004_api_host'],
        $order_info['payment_method']['processor_params']['factoring004_login'],
        $order_info['payment_method']['processor_params']['factoring004_password'],
        isset($order_info['payment_method']['processor_params']['factoring004_debug_mode']),
    );

    fn_redirect($preApp->preApp($order_info), true, true);

} catch (Exception $e) {
    fn_redirect(fn_url('factoring004-errorpage'));
}

exit;
