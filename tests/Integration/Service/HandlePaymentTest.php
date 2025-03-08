<?php

namespace App\Tests\Integration\Service;

use App\DTO\PaymentRequestDTO;
use App\Service\HandlePayment;
use App\Service\PaymentProcessor\PaymentProcessorFactory;
use App\Service\PaymentProcessor\PaymentProcessorInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HandlePaymentTest extends TestCase
{
    public function testProcessPaymentSuccess()
    {
        // Mock PaymentProcessorInterface
        $paymentProcessorMock = $this->createMock(PaymentProcessorInterface::class);
        $paymentProcessorMock->method('processPayment')
            ->willReturn([
                'transactionId' => '12345',
                'dateOfCreating' => '2025-03-08 08:04:17.516+0000',
                'amount' => '100.00',
                'currency' => 'USD',
                'cardBin' => '401200',
            ]);

        // Mock HttpClientInterface
        $httpClientMock = $this->createMock(HttpClientInterface::class);

        // Mock PaymentProcessorFactory to return the mocked processor
        $factoryMock = $this->createMock(PaymentProcessorFactory::class);
        $factoryMock->method('create')
            ->willReturn($paymentProcessorMock);

        // Create HandlePayment service and inject the mocked factory
        $handlePayment = new HandlePayment($httpClientMock, $factoryMock);

        // Create a PaymentRequestDTO
        $paymentRequest = new PaymentRequestDTO([
            'amount' => 100.00,
            'currency' => 'USD',
            'card_number' => '4012000100000007',
            'card_exp_year' => '2035',
            'card_exp_month' => '05',
            'card_cvv' => '123',
        ]);

        // Call the method
        $response = $handlePayment->processPayment('shift4', $paymentRequest);

        // Assert the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(
            '{"status":"success","data":{"transactionId":"12345","dateOfCreating":"2025-03-08 08:04:17.516+0000","amount":"100.00","currency":"USD","cardBin":"401200"}}',
            $response->getContent()
        );
    }

    public function testProcessPaymentError()
    {
        // Mock PaymentProcessorInterface to return an error
        $paymentProcessorMock = $this->createMock(PaymentProcessorInterface::class);
        $paymentProcessorMock->method('processPayment')
            ->willReturn(['error' => 'Invalid card']);

        // Mock HttpClientInterface
        $httpClientMock = $this->createMock(HttpClientInterface::class);

        // Mock PaymentProcessorFactory to return the mocked processor
        $factoryMock = $this->createMock(PaymentProcessorFactory::class);
        $factoryMock->method('create')
            ->willReturn($paymentProcessorMock);

        // Create HandlePayment service and inject the mocked factory
        $handlePayment = new HandlePayment($httpClientMock, $factoryMock);

        // Create a PaymentRequestDTO
        $paymentRequest = new PaymentRequestDTO([
            'amount' => 100.00,
            'currency' => 'USD',
            'card_number' => '4012000100000007',
            'card_exp_year' => '2035',
            'card_exp_month' => '05',
            'card_cvv' => '123',
        ]);

        // Call the method
        $response = $handlePayment->processPayment('shift4', $paymentRequest);

        // Assert the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(
            '{"status":"error","data":{"error":"Invalid card"}}',
            $response->getContent()
        );
    }
}