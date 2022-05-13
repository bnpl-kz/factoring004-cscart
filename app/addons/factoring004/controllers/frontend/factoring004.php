<?php

/** @var string $mode */

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

/** @var Tygh\Database\Connection $db */
$db = Tygh::$app['db'];

$order = $db->getRow(<<<'SQL'
    SELECT ?:orders.order_id, ?:orders.status
        FROM ?:orders
        INNER JOIN ?:payments ON ?:payments.payment_id = ?:orders.payment_id
        INNER JOIN ?:payment_processors ON ?:payment_processors.processor_id = ?:payments.processor_id
            AND ?:payment_processors.processor_script = 'factoring004.php'
        WHERE ?:orders.order_id = ?i
SQL, $request['billNumber']);

if (!$order) {
    return;
}

if ($request['status'] === 'preapproved') {
    header('Content-Type: application/json');
    echo json_encode(['response' => 'preapproved']);
    exit;
}

if ($request['status'] === 'completed') {
    $status = OrderStatuses::PAID;
    $response = 'ok';
} else {
    $status = OrderStatuses::FAILED;
    $response = 'declined';
}

if ($order['status'] === OrderStatuses::OPEN) {
    $changed = fn_change_order_status((int) $request['billNumber'], $status);

    if (!$changed) {
        return;
    }
}

header('Content-Type: application/json');
echo json_encode(compact('response'));
exit;
