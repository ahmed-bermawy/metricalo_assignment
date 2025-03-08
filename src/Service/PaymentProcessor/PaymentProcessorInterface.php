<?php

namespace App\Service\PaymentProcessor;

use App\DTO\PaymentRequestDTO;

interface PaymentProcessorInterface
{
    public function processPayment(PaymentRequestDTO $paymentRequest): array;
}