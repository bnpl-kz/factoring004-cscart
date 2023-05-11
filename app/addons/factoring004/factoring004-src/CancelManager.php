<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004Payment;

use BnplPartners\Factoring004\ChangeStatus\CancelOrder;
use BnplPartners\Factoring004\ChangeStatus\CancelStatus;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Response\ErrorResponse;

class CancelManager
{
    use ApiManager;

    /**
     * @var array<string, mixed>
     */
    private $processorParams;

    /**
     * @param array<string, mixed> $processorParams
     */
    public function __construct(array $processorParams)
    {
        $this->processorParams = $processorParams;
        $this->createApi(
            $processorParams['factoring004_api_host'],
            $processorParams['factoring004_login'],
            $processorParams['factoring004_password']
        );
    }

    /**
     * @param array<string, mixed> $processorParams
     */
    public static function create(array $processorParams): CancelManager
    {
        return new static($processorParams);
    }

    /**
     * @param int|string $orderId
     *
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function cancel($orderId): void
    {
        $response = $this->api->changeStatus->changeStatusJson([
            new MerchantsOrders($this->processorParams['factoring004_partner_code'], [
                new CancelOrder((string) $orderId, CancelStatus::CANCEL()),
            ]),
        ]);

        foreach ($response->getErrorResponses() as $errorResponse) {
            throw new ErrorResponseException(new ErrorResponse(
                $errorResponse->getCode(),
                $errorResponse->getMessage(),
                null,
                null,
                $errorResponse->getError(),
            ));
        }
    }
}
