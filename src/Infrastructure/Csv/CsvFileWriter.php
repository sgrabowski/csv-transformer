<?php

declare(strict_types=1);

namespace App\Infrastructure\Csv;

use App\Application\OutputHandler\Csv\CsvWriter;

class CsvFileWriter implements CsvWriter
{
    private mixed $fileHandle;
    private string $delimiter = ',';
    private string $eol = "\r\n";

    public function __construct(string $filePath)
    {
        $this->fileHandle = \fopen($filePath, 'wb');

        if ($this->fileHandle === false) {
            throw new \RuntimeException("Unable to open file for writing: {$filePath}");
        }

        $this->writeBom();
    }

    public function writeHeaders(array $headers): void
    {
        $this->writeLine($headers);
    }

    public function write(array $data): void
    {
        $this->writeLine($data);
    }

    private function writeBom(): void
    {
        \fwrite($this->fileHandle, "\xEF\xBB\xBF");
    }

    /**
     * @param array<string> $fields
     */
    private function writeLine(array $fields): void
    {
        $line = \implode($this->delimiter, $fields) . $this->eol;

        if (\fwrite($this->fileHandle, $line) === false) {
            throw new \RuntimeException('Unable to write to CSV file.');
        }
    }

    public function finish(): void
    {
        \fclose($this->fileHandle);
    }

    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }
}
