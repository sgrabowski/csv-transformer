<?php

declare(strict_types=1);

namespace App\Infrastructure\Csv;

use App\Application\InputProvider\Csv\CsvParser;

class CsvFileParser implements CsvParser
{
    private string $filePath;
    private mixed $fileHandle;
    private bool $treatEmptyStringsAsNull = true;

    /**
     * @var array<string>
     */
    private array $headers;

    public function __construct(string $filePath, private readonly string $delimiter = ',')
    {
        $this->filePath = $filePath;
        $this->fileHandle = \fopen($this->filePath, 'rb');

        if ($this->fileHandle === false) {
            throw new \RuntimeException("Unable to open file for reading: {$this->filePath}");
        }

        $firstLine = $this->readRawLine();
        if ($firstLine === null) {
            throw new \RuntimeException('CSV file is empty or headers are missing.');
        }

        $firstLine = $this->removeBom($firstLine);
        $this->headers = \str_getcsv($firstLine, $this->delimiter);

        if (\count($this->headers) === 0) {
            throw new \RuntimeException('CSV file is empty or headers are missing.');
        }
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function nextLine(): ?array
    {
        $data = $this->readLine();

        if ($data === null) {
            // End of file reached
            $this->finish();

            return null;
        }

        if ($this->treatEmptyStringsAsNull) {
            $data = $this->replaceEmptyStringsWithNull($data);
        }

        return $data;
    }

    private function readRawLine(): ?string
    {
        $line = \fgets($this->fileHandle);

        return $line === false ? null : $line;
    }

    private function removeBom(string $line): string
    {
        $bom = "\xEF\xBB\xBF";
        if (\strncmp($line, $bom, 3) === 0) {
            // BOM detected, remove it
            $line = \substr($line, 3);
        }

        return $line;
    }

    /**
     * @return array<string>|null
     */
    private function readLine(): ?array
    {
        $data = \fgetcsv($this->fileHandle, 0, $this->delimiter);

        return $data ? $data : null;
    }

    private function finish(): void
    {
        if (\is_resource($this->fileHandle)) {
            \fclose($this->fileHandle);
        }
    }

    /**
     * @param array<string> $data
     *
     * @return array<string, null>
     */
    private function replaceEmptyStringsWithNull(array $data): array
    {
        return \array_map(static function ($value) {
            return $value === '' ? null : $value;
        }, $data);
    }
}
