<?php

namespace App\Command;

use App\DTO\PaymentRequestDTO;
use App\Service\HandlePayment;
use App\Trait\CommandFormater;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpFoundation\Response;

#[AsCommand(
    name: 'app:process-payment',
    description: 'Process a payment through a selected provider.'
)]
class PaymentCommand extends Command
{
    use CommandFormater;
    public function __construct(private readonly HandlePayment $handlePayment)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initializeFormatting($output);

        // Ask the user to select a provider
        $helper = $this->getHelper('question');
        $providerQuestion = new ChoiceQuestion(
            'Please select the payment provider (default: shift4):',
            ['shift4', 'aci'],
            0
        );
        $provider = $helper->ask($input, $output, $providerQuestion);

        // Ask the user for payment details
        $amountQuestion = new Question('Please enter the amount (e.g., 100.00): ');
        $amount = $helper->ask($input, $output, $amountQuestion);

        $currencyQuestion = new Question('Please enter the currency (e.g., USD): ');
        $currency = $helper->ask($input, $output, $currencyQuestion);

        $cardNumberQuestion = new Question('Please enter the card number (e.g., 4012000100000007): ');
        $cardNumber = $helper->ask($input, $output, $cardNumberQuestion);

        $cardExpYearQuestion = new Question('Please enter the card expiration year (e.g., 2035): ');
        $cardExpYear = $helper->ask($input, $output, $cardExpYearQuestion);

        $cardExpMonthQuestion = new Question('Please enter the card expiration month (e.g., 05): ');
        $cardExpMonth = $helper->ask($input, $output, $cardExpMonthQuestion);

        $cardCvvQuestion = new Question('Please enter the card CVV (e.g., 123): ');
        $cardCvv = $helper->ask($input, $output, $cardCvvQuestion);

        // Prepare the request body
        $requestBody = [
            'amount' => (float) $amount,
            'currency' => $currency,
            'card_number' => $cardNumber,
            'card_exp_year' => $cardExpYear,
            'card_exp_month' => $cardExpMonth,
            'card_cvv' => $cardCvv,
        ];

        try {
            $paymentRequest = new PaymentRequestDTO($requestBody);
            $response = $this->handlePayment->processPayment($provider, $paymentRequest);
            $responseData = json_decode($response->getContent(), true);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                $output->writeln('<error>Payment processed failed:</error>');
                $this->outputResponse( $output,$response, $responseData);
                return Command::FAILURE;
            }

            $output->writeln('<success>Payment processed successfully:</success>');
            $this->outputResponse($output, $response, $responseData);
        } catch (\Exception $e) {
            $output->writeln('<error>An error occurred:</error> ' . $e->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

}