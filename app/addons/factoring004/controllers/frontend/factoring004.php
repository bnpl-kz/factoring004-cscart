<?php

require_once DIR_ROOT . '/app/addons/factoring004/vendor/autoload.php';

/** @var string $mode */

use BnplPartners\Factoring004\Exception\InvalidSignatureException;
use BnplPartners\Factoring004\Signature\PostLinkSignatureValidator;
use BnplPartners\Factoring004Payment\LoggerFactory;
use Tygh\Enum\OrderStatuses;

if ($mode !== 'index' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

try {
    $request = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    return;
}

if (empty($request['status']) || !is_string($request['status'])) {
    return;
}

if (empty($request['billNumber']) || !is_string($request['billNumber'])) {
    return;
}

if (empty($request['preappId']) || !is_string($request['preappId'])) {
    return;
}

$order = fn_get_order_info((int) $request['billNumber']);

if (!$order) {
    return;
}

$processorParams = $order['payment_method']['processor_params'];

if (strpos(array_key_first($processorParams), 'factoring004') === false) {
    return;
}

LoggerFactory::create()
    ->setDebug(isset($processorParams['factoring004_debug_mode']))
    ->createLogger()
    ->debug(json_encode($request));

$validator = new PostLinkSignatureValidator($processorParams['factoring004_partner_code']);

try {
    $validator->validateData($request);
} catch (InvalidSignatureException $e) {
    return;
}

if ($request['status'] === 'preapproved') {
    header('Content-Type: application/json');
    echo json_encode(['response' => 'preapproved']);
    exit;
}

if ($request['status'] === 'completed') {
    fn_change_order_status($order['order_id'], OrderStatuses::PAID);
    $response = 'ok';
} else {
    fn_change_order_status($order['order_id'], OrderStatuses::FAILED);
    $response = 'declined';
}

header('Content-Type: application/json');
echo json_encode(compact('response'));
exit;
