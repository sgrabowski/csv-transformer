<?php

declare(strict_types=1);

namespace App\Application\InputProvider\Csv;

interface CsvParser
{
    /**
     * Returns column names as array
     * Array keys MUST be the same as in nextLine method.
     *
     * @return array<string>
     */
    public function getHeaders(): array;

    /**
     * Returns next CSV line as array
     * Returns null if there is no next line
     * Array keys MUST be the same as in getHeaders method.
     *
     * @return array<string>
     */
    public function nextLine(): ?array;
}
