<?php

namespace App\Controller\Api;

use App\DTO\PaymentRequestDTO;
use App\Service\HandlePayment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{

    public function __construct(private readonly HandlePayment $handlePayment)
    {
    }

    #[Route('/api/payment/process/{provider}', name: 'api_payment_process', methods: ['POST'])]
    public function process(string $provider, Request $request): JsonResponse
    {
        $paymentRequest = new PaymentRequestDTO($request);
        return $this->handlePayment->processPayment($provider, $paymentRequest);
    }

}