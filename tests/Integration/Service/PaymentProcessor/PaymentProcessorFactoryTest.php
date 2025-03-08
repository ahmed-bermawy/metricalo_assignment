<?php

namespace App\Tests\Integration\Service\PaymentProcessor;

use App\Service\PaymentProcessor\ACIPaymentProcessor;
use App\Service\PaymentProcessor\PaymentProcessorFactory;
use App\Service\PaymentProcessor\Shift4PaymentProcessor;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentProcessorFactoryTest extends TestCase
{
    public function testCreateShift4Processor()
    {
        // Mock HttpClientInterface
        $httpClientMock = $this->createMock(HttpClientInterface::class);

        // Create the factory
        $factory = new PaymentProcessorFactory();

        // Call the method
        $processor = $factory->create('shift4', $httpClientMock);

        // Assert the result
        $this->assertInstanceOf(Shift4PaymentProcessor::class, $processor);
    }

    public function testCreateACIProcessor()
    {
        // Mock HttpClientInterface
        $httpClientMock = $this->createMock(HttpClientInterface::class);

        // Create the factory
        $factory = new PaymentProcessorFactory();

        // Call the method
        $processor = $factory->create('aci', $httpClientMock);

        // Assert the result
        $this->assertInstanceOf(ACIPaymentProcessor::class, $processor);
    }

    public function testCreateInvalidProcessor()
    {
        // Mock HttpClientInterface
        $httpClientMock = $this->createMock(HttpClientInterface::class);

        // Create the factory
        $factory = new PaymentProcessorFactory();

        // Expect an InvalidArgumentException to be thrown
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid payment provider');

        // Call the method with an invalid type
        $factory->create('invalid', $httpClientMock);
    }
}