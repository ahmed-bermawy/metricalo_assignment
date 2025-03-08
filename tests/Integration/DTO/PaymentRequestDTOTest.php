<?php

namespace App\Tests\Integration\DTO;

use App\DTO\PaymentRequestDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class PaymentRequestDTOTest extends TestCase
{
    public function testPaymentRequestDTOFromRequest()
    {
        // Mock a Request object
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

        // Create the DTO
        $dto = new PaymentRequestDTO($requestMock);

        // Assert the values
        $this->assertEquals(100.00, $dto->amount);
        $this->assertEquals('USD', $dto->currency);
        $this->assertEquals('4012000100000007', $dto->cardNumber);
        $this->assertEquals('2035', $dto->cardExpiryYear);
        $this->assertEquals('05', $dto->cardExpiryMonth);
        $this->assertEquals('123', $dto->cardCVV);
    }

    public function testPaymentRequestDTOFromArray()
    {
        // Create the DTO from an array
        $data = [
            'amount' => 100.00,
            'currency' => 'USD',
            'card_number' => '4012000100000007',
            'card_exp_year' => '2035',
            'card_exp_month' => '05',
            'card_cvv' => '123',
        ];
        $dto = new PaymentRequestDTO($data);

        // Assert the values
        $this->assertEquals(100.00, $dto->amount);
        $this->assertEquals('USD', $dto->currency);
        $this->assertEquals('4012000100000007', $dto->cardNumber);
        $this->assertEquals('2035', $dto->cardExpiryYear);
        $this->assertEquals('05', $dto->cardExpiryMonth);
        $this->assertEquals('123', $dto->cardCVV);
    }
}