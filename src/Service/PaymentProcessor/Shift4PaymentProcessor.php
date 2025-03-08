<?php

namespace App\Service\PaymentProcessor;

use App\DTO\PaymentRequestDTO;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Shift4PaymentProcessor implements PaymentProcessorInterface
{
    private HttpClientInterface $httpClient;

    private string $apiUrl = 'https://api.shift4.com/charges';
    private string $authKey = 'sk_test_EprTbpA2dwy96c4DvtbMJzeC';
    private string $customerId = 'cust_QscUuqliAmA37tSpJjoAV8Yj';

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function processPayment(PaymentRequestDTO $paymentRequest): array
    {
        $requestData = [
            'amount' => (int) ($paymentRequest->getAmount() * 100),
            'currency' => $paymentRequest->getCurrency(),
            'customerId' => $this->customerId, // Hardcoded for test
            'card' => [
                'number' => $paymentRequest->getCardNumber(),
                'expMonth' => $paymentRequest->getCardExpiryMonth(),
                'expYear' => $paymentRequest->getCardExpiryYear(),
                'cvc' => $paymentRequest->getCardCVV(),
            ],
        ];

        try {
            $response = $this->httpClient->request('POST', $this->apiUrl, [
                'auth_basic' => [$this->authKey, ''],
                'body' => http_build_query($requestData),
            ]);

            $data = $response->toArray();

            return [
                'transactionId' => $data['id'],
                'dateOfCreating' => (new \DateTime())->setTimestamp($data['created'])->format('Y-m-d H:i:s.vO'),
                'amount' => $paymentRequest->getAmount(),
                'currency' => $paymentRequest->getCurrency(),
                'cardBin' => $data['card']['first6'],
            ];
        } catch (TransportExceptionInterface | ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | DecodingExceptionInterface $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}