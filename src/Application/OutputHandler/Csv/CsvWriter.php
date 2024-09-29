<?php

declare(strict_types=1);

namespace App\Application\OutputHandler\Csv;

interface CsvWriter
{
    public function writeHeaders(string $headers): void;

    public function write(string $data): void;
}
