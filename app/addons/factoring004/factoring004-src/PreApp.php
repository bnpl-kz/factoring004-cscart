<?php

namespace BnplPartners\Factoring004Payment;

use BnplPartners\Factoring004\Exception\PackageException;
use BnplPartners\Factoring004\PreApp\PreAppMessage;

final class PreApp
{
    use ApiManager;

    public function __construct($baseUrl, $token)
    {
        $this->createApi($baseUrl, $token);
    }

    public function preApp($data)
    {
        try {
            $message = PreAppMessage::createFromArray([
                'partnerData' => [
                    'partnerName' => (string) $data['payment_method']['processor_params']['factoring004_partner_name'],
                    'partnerCode' => (string) $data['payment_method']['processor_params']['factoring004_partner_code'],
                    'pointCode' => (string) $data['payment_method']['processor_params']['factoring004_point_code'],
                    'partnerEmail' => (string) $data['payment_method']['processor_params']['factoring004_partner_email'],
                    'partnerWebsite' => (string) $data['payment_method']['processor_params']['factoring004_partner_website'],
                ],
                'billNumber' => (string) $data['order_id'],
                'billAmount' => (int) ceil($data['total']),
                'itemsQuantity' => array_sum(array_map(function ($item) {
                    return $item['amount'];
                }, $data['products'])),
                'successRedirect' => (defined('HTTPS') ? 'https://' : 'http://') . REAL_HOST,
                'failRedirect' => (defined('HTTPS') ? 'https://' : 'http://') . REAL_HOST,
                'phoneNumber' => empty($data['phone']) ? null : preg_replace('/[^0-9]/','', $data['phone']),
                'postLink' => fn_url('factoring004'),
                'items' => array_values(array_map(function ($item) {
                    return [
                        'itemId' => (string)$item['product_id'],
                        'itemName' => (string)$item['product'],
                        'itemCategory' => (string) $item['product'],
                        'itemQuantity' => (int)$item['amount'],
                        'itemPrice' => (int)ceil($item['price']),
                        'itemSum' => (int)ceil($item['price']) * (int)$item['amount'],
                    ];
                }, $data['products'])),
                'deliveryPoint' => [
                    'region' => (string) $data['b_state'],
                    'city' => (string) $data['b_city'],
                    'street' => $data['b_address'] . ' ' . $data['b_address_2']
                ]
            ]);

            return $this->api->preApps->preApp($message)->getRedirectLink();

        } catch (PackageException $e) {
            $this->log($e);
        }
    }
}