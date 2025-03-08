<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Type('float')]
    public float $amount;

    #[Assert\NotBlank]
    #[Assert\Currency]
    public string $currency;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $cardNumber;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $cardExpiryYear;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $cardExpiryMonth;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 4)]
    public string $cardCVV;

    public function __construct($request)
    {
        $payload = is_array($request) ? $request : json_decode($request->getContent(), true);
        $this->amount = $payload['amount'];
        $this->currency = $payload['currency'];
        $this->cardNumber = $payload['card_number'];
        $this->cardExpiryYear = $payload['card_exp_year'];
        $this->cardExpiryMonth = $payload['card_exp_month'];
        $this->cardCVV = $payload['card_cvv'];
    }

    public function getAmount(): string
    {
        return number_format($this->amount, 2, '.', '');
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    public function getCardExpiryYear(): string
    {
        return $this->cardExpiryYear;
    }

    public function getCardExpiryMonth(): string
    {
        return $this->cardExpiryMonth;
    }

    public function getCardCVV(): string
    {
        return $this->cardCVV;
    }
}