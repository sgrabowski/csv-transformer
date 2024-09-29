<?php

declare(strict_types=1);

namespace App\Application\OutputHandler\Csv;

interface CsvWriter
{
    /**
     * @param array<string> $headers
     */
    public function writeHeaders(array $headers): void;

    /**
     * @param array<string> $data
     */
    public function write(array $data): void;

    public function finish(): void;

    public function setDelimiter(string $delimiter): void;
}
