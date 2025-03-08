<?php

namespace App\Service;

use App\DTO\PaymentRequestDTO;
use App\Service\PaymentProcessor\PaymentProcessorFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HandlePayment
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly PaymentProcessorFactory $paymentProcessorFactory
    )
    {
    }

    public function processPayment(string $provider, PaymentRequestDTO $paymentRequest): JsonResponse
    {
        $paymentProcessor = $this->paymentProcessorFactory->create($provider, $this->httpClient);
        $result = $paymentProcessor->processPayment($paymentRequest);

        if (isset($result['error'])) {
           return new JsonResponse(['status' => 'error','data' => $result], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['status' => 'success','data' => $result]);
    }
}