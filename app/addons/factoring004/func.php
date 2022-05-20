<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function getShippings()
{
    return fn_get_shippings(true);
}

/**
 * @param array<string, mixed> $forceNotification
 * @param array<string, mixed> $orderInfo
 *
 * @throws \Exception
 */
function fn_factoring004_change_order_status_before_update_product_amount(
    int $orderId,
    string $statusTo,
    string $statusFrom,
    array $forceNotification,
    bool $placeOrder,
    array $orderInfo
): void {
    if ($statusTo !== \Tygh\Enum\OrderStatuses::CANCELED) {
        return;
    }

    $processorParams = $orderInfo['payment_method']['processor_params'];

    if (strpos(array_key_first($processorParams), 'factoring004') === false) {
        return;
    }

    require_once DIR_ROOT . '/app/addons/factoring004/vendor/autoload.php';

    try {
        \BnplPartners\Factoring004Payment\CancelManager::create($processorParams)->cancel($orderId);
    } catch (\BnplPartners\Factoring004\Exception\ErrorResponseException $e) {
        fn_set_notification('E', 'Cancel order #' . $orderId, $e->getErrorResponse()->getMessage());
        throw $e;
    } catch (\Exception $e) {
        $message = (defined('DEVELOPMENT') && DEVELOPMENT) ? $e->getMessage() : 'An error occurred';

        fn_set_notification('E', 'Cancel order #' . $orderId, $message);
        throw $e;
    }
}

/**
 * @return array<string, string>
 */
function getFactoring004Translations(): array
{
    return \Tygh\Languages\Values::getLangVarsByPrefix('payments.factoring004');
}
