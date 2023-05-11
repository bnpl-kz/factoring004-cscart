<?php

require_once DIR_ROOT . '/app/addons/factoring004/vendor/autoload.php';

/** @var string $mode */

use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\ChangeStatus\DeliveryOrder;
use BnplPartners\Factoring004\ChangeStatus\DeliveryStatus;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\PackageException;
use BnplPartners\Factoring004\OAuth\CacheOAuthTokenManager;
use BnplPartners\Factoring004\OAuth\OAuthTokenManager;
use BnplPartners\Factoring004\Otp\SendOtp;
use BnplPartners\Factoring004\Response\ErrorResponse;
use BnplPartners\Factoring004\Transport\GuzzleTransport;
use BnplPartners\Factoring004Payment\LoggerFactory;
use Desarrolla2\Cache\File as FileCache;

if ($mode !== 'index' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

if (empty($_POST['order_id']) || !is_string($_POST['order_id'])) {
    return;
}

$order = fn_get_order_info((int) $_POST['order_id']);

if (!$order) {
    return;
}

$processorParams = $order['payment_method']['processor_params'];

if (strpos(array_key_first($processorParams), 'factoring004') === false) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'otp' => false]);
    exit;
}

$confirmableShippingMethods = array_keys($processorParams['factoring004_delivery_methods']);
$orderShippingMethods = array_map(function (array $shipping) {
    return $shipping['shipping_id'];
}, $order['shipping']);

$logger = (new LoggerFactory())
    ->setDebug(isset($processorParams['factoring004_debug_mode']))
    ->createLogger();

$transport = new GuzzleTransport();
$transport->setLogger($logger);

$auth_path = '/users/api/v1';
$cache_key = 'factoring004';

$tokenManager = new OAuthTokenManager(
    $processorParams['factoring004_api_host'].$auth_path,
    $processorParams['factoring004_login'],
    $processorParams['factoring004_password']
);
$cache = new FileCache(DIR_ROOT . "/app/addons/factoring004/");
$cacheTokenManager = new CacheOAuthTokenManager($tokenManager, $cache, $cache_key);

$api = Api::create(
    $processorParams['factoring004_api_host'],
    new BearerTokenAuth($cacheTokenManager->getAccessToken()->getAccess()),
    $transport,
);

header('Content-Type: application/json');

try {
    // Delivery with OTP
    if (array_intersect($confirmableShippingMethods, $orderShippingMethods)) {
        $response = $api->otp->sendOtp(
            new SendOtp($processorParams['factoring004_partner_code'], $order['order_id'], (int) ceil($order['total']))
        );

        echo json_encode(['success' => true, 'otp' => true, 'message' => $response->getMsg()]);
        exit;
    }

    // Delivery without OTP
    $response = $api->changeStatus->changeStatusJson([
        new MerchantsOrders($processorParams['factoring004_partner_code'], [
            new DeliveryOrder($order['order_id'], DeliveryStatus::DELIVERED(), (int) ceil($order['total'])),
        ]),
    ]);

    foreach ($response->getSuccessfulResponses() as $successResponse) {
        echo json_encode(['success' => true, 'otp' => false, 'message' => $successResponse->getMsg()]);
        exit;
    }

    foreach ($response->getErrorResponses() as $errorResponse) {
        throw new ErrorResponseException(new ErrorResponse(
            $errorResponse->getCode(),
            $errorResponse->getMessage(),
            null,
            null,
            $errorResponse->getError(),
        ));
    }
} catch (ErrorResponseException $e) {
    $logger->error($e);

    echo json_encode(['success' => false, 'error' => $e->getErrorResponse()->getMessage()]);
    exit;
} catch (PackageException $e) {
    $logger->error($e);

    if (defined('DEVELOPMENT') && DEVELOPMENT) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } else {
        echo json_encode(['success' => false, 'error' => __('payments.factoring004.error_occurred')]);
    }

    exit;
}
