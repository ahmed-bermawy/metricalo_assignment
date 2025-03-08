<?php

namespace App\Service\PaymentProcessor;

use InvalidArgumentException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentProcessorFactory
{
    public function create(string $type, HttpClientInterface $httpClient): PaymentProcessorInterface
    {
        return match ($type) {
            'shift4' => new Shift4PaymentProcessor($httpClient),
            'aci' => new ACIPaymentProcessor($httpClient),
            default => throw new InvalidArgumentException('Invalid payment provider'),
        };
    }
}