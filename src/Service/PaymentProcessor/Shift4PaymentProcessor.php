<?php

namespace App\Service\PaymentProcessor;

use App\DTO\PaymentRequestDTO;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Shift4PaymentProcessor implements PaymentProcessorInterface
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function processPayment(PaymentRequestDTO $paymentRequest): array
    {
        // Hardcoded test data for Shift4
        $response = $this->httpClient->request('POST', 'https://api.shift4.com/charge', [
            'json' => [
                'amount' => $paymentRequest->getAmount(),
                'currency' => $paymentRequest->getCurrency(),
                'card' => [
                    'number' => '4242424242424242',
                    'expYear' => $paymentRequest->getCardExpiryYear()
,                    'expMonth' => $paymentRequest->getCardExpiryMonth(),
                    'cvv' => $paymentRequest->getCardCVV(),
                ],
                'authKey' => 'test_auth_key', // Hardcoded for test
            ],
        ]);

        $data = $response->toArray();

        return [
            'transactionId' => $data['id'],
            'date' => $data['created'],
            'amount' => $paymentRequest->amount,
            'currency' => $paymentRequest->currency,
            'cardBin' => substr($paymentRequest->cardNumber, 0, 6),
        ];
    }
}