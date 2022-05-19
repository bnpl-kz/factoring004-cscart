<?php

require_once DIR_ROOT . '/app/addons/factoring004/vendor/autoload.php';

/** @var string $mode */

use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\PackageException;
use BnplPartners\Factoring004\Otp\CheckOtp;
use BnplPartners\Factoring004\Transport\GuzzleTransport;
use BnplPartners\Factoring004Payment\LoggerFactory;

if ($mode !== 'index' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

if (empty($_POST['order_id']) || !is_string($_POST['order_id'])) {
    return;
}

if (empty($_POST['otp']) || !is_string($_POST['otp'])) {
    return;
}

$order = fn_get_order_info((int) $_POST['order_id']);

if (!$order) {
    return;
}

$processorParams = $order['payment_method']['processor_params'];

if (strpos(array_key_first($processorParams), 'factoring004') === false) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Payment method is not factoring004']);
    exit;
}

$logger = (new LoggerFactory())
    ->setDebug(isset($processorParams['factoring004_debug_mode']))
    ->createLogger();

$transport = new GuzzleTransport();
$transport->setLogger($logger);

$api = Api::create(
    $processorParams['factoring004_api_host'],
    new BearerTokenAuth($processorParams['factoring004_delivery_token']),
    $transport,
);

header('Content-Type: application/json');

try {
    $response = $api->otp->checkOtp(
        new CheckOtp(
            $processorParams['factoring004_partner_code'],
            $order['order_id'],
            $_POST['otp'],
            (int) ceil($order['total'])
        )
    );

    echo json_encode(['success' => true, 'otp' => true, 'message' => $response->getMsg()]);
    exit;
} catch (ErrorResponseException $e) {
    $logger->error($e);

    echo json_encode(['success' => false, 'error' => $e->getErrorResponse()->getMessage()]);
    exit;
} catch (PackageException $e) {
    $logger->error($e);

    if (defined('DEVELOPMENT') && DEVELOPMENT) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } else {
        echo json_encode(['success' => false, 'error' => 'An error occurred']);
    }

    exit;
}
