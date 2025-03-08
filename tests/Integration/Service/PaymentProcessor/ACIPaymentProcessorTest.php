<?php

namespace App\Tests\Integration\Service\PaymentProcessor;

use App\DTO\PaymentRequestDTO;
use App\Service\PaymentProcessor\ACIPaymentProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ACIPaymentProcessorTest extends TestCase
{
    public function testProcessPaymentSuccess()
    {
        // Mock HttpClientInterface and ResponseInterface
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('toArray')
            ->willReturn([
                'id' => '12345',
                'timestamp' => '2025-03-08 08:04:17.516+0000',
                'card' => ['bin' => '401200'],
            ]);

        $httpClientMock->method('request')
            ->willReturn($responseMock);

        // Create the processor
        $processor = new ACIPaymentProcessor($httpClientMock);

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
        $result = $processor->processPayment($paymentRequest);

        // Assert the result
        $this->assertEquals([
            'transactionId' => '12345',
            'dateOfCreating' => '2025-03-08 08:04:17.516+0000',
            'amount' => '100.00',
            'currency' => 'USD',
            'cardBin' => '401200',
        ], $result);
    }
}