<?php

namespace App\Trait;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;

trait CommandFormater
{
    public function initializeFormatting($output): void
    {
        $formatter = $output->getFormatter();
        $formatter->setStyle('success', new OutputFormatterStyle('black', 'green'));
        $formatter->setStyle('error', new OutputFormatterStyle('white', 'red'));
        $formatter->setStyle('json_key', new OutputFormatterStyle('yellow')); // Yellow for JSON keys
        $formatter->setStyle('json_string', new OutputFormatterStyle('green')); // Green for JSON strings
        $formatter->setStyle('json_number', new OutputFormatterStyle('cyan')); // Cyan for JSON numbers
        $formatter->setStyle('json_boolean', new OutputFormatterStyle('magenta')); // Magenta for JSON booleans
    }

    public function outputResponse($output, $response, $responseData): void
    {
        if (json_last_error() === JSON_ERROR_NONE) {
            $output->writeln($this->formatJson($responseData));
        } else {
            $output->writeln($response->getContent());
        }
    }


    private function formatJson(array $data): string
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);

        // Apply styles to JSON keys, strings, numbers, and booleans
        $json = preg_replace('/"([^"]+)":/', '<json_key>"$1"</json_key>:', $json); // Style keys
        $json = preg_replace('/:\s*"([^"]+)"/', ': <json_string>"$1"</json_string>', $json); // Style strings
        $json = preg_replace('/:\s*(\d+)/', ': <json_number>$1</json_number>', $json); // Style numbers
        return preg_replace('/:\s*(true|false)/', ': <json_boolean>$1</json_boolean>', $json); // Style booleans
    }
}