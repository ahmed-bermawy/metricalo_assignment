<?php

namespace App\Service\PaymentProcessor;

use App\DTO\PaymentRequestDTO;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ACIPaymentProcessor implements PaymentProcessorInterface
{
    private HttpClientInterface $httpClient;
    private string $authKey='OGFjN2E0Yzc5Mzk0YmRjODAxOTM5NzM2ZjFhNzA2NDF8enlac1lYckc4QXk6bjYzI1NHNng=';
    private string $entityId='8ac7a4c79394bdc801939736f17e063d';
    private string $apiUrl='https://eu-test.oppwa.com/v1/payments';

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function processPayment(PaymentRequestDTO $paymentRequest): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->authKey,
        ];

        $requestData = [
            'entityId' => $this->entityId,
            'amount' => $paymentRequest->getAmount(),
            'currency' => $paymentRequest->getCurrency(),
            'paymentBrand' => 'VISA', // Hardcoded for test
            'paymentType' => 'PA', // Hardcoded for test
            'card.number' => $paymentRequest->getCardNumber(),
            'card.holder' => 'Jane Jones', // Hardcoded for test
            'card.expiryYear' => $paymentRequest->getCardExpiryYear(),
            'card.expiryMonth' => $paymentRequest->getCardExpiryMonth(),
            'card.cvv' => $paymentRequest->getCardCVV(),
        ];

        try {
            $response = $this->httpClient->request('POST', $this->apiUrl, [
                'headers' => $headers,
                'body' => http_build_query($requestData),
            ]);

            $data = $response->toArray();

            return [
                'transactionId' => $data['id'],
                'dateOfCreating' => $data['timestamp'],
                'amount' => $paymentRequest->getAmount(),
                'currency' => $paymentRequest->getCurrency(),
                'cardBin' => $data['card']['bin'],
            ];
        } catch (TransportExceptionInterface | ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | DecodingExceptionInterface $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }

    }
}