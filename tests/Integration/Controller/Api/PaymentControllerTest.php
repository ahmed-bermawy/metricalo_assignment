<?php

namespace App\Tests\Integration\Controller\Api;

use App\Controller\Api\PaymentController;
use App\Service\HandlePayment;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentControllerTest extends TestCase
{
    public function testProcessPayment()
    {
        $handlePaymentMock = $this->createMock(HandlePayment::class);
        $handlePaymentMock->expects($this->once())
            ->method('processPayment')
            ->willReturn(new JsonResponse(['status' => 'success']));

        // Create a mock Request object
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getContent')
            ->willReturn(json_encode([
                'amount' => 100.00,
                'currency' => 'USD',
                'card_number' => '4012000100000007',
                'card_exp_year' => '2035',
                'card_exp_month' => '05',
                'card_cvv' => '123',
            ]));

        // Create the controller and call the method
        $controller = new PaymentController($handlePaymentMock);
        $response = $controller->process('shift4', $requestMock);

        // Assert the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('{"status":"success"}', $response->getContent());

    }
}