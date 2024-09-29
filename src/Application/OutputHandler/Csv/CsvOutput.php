<?php

declare(strict_types=1);

namespace App\Application\OutputHandler\Csv;

use App\Core\OutputHandler;
use App\Core\Value\Record;

class CsvOutput implements OutputHandler
{
    private bool $headersWritten = false;

    public function __construct(private readonly CsvWriter $csvWriter)
    {
    }

    public function handle(Record $record): void
    {
        if (!$this->headersWritten) {
            $this->csvWriter->writeHeaders($this->extractHeaders($record));
            $this->headersWritten = true;
        }

        $this->csvWriter->write($this->extractValues($record));
    }

    private function extractHeaders(Record $record): string
    {
        $values = [];

        foreach ($record->fields as $field) {
            $values[] = $field->groupName;
        }

        return $this->formatInCsv($values);
    }

    private function extractValues(Record $record): string
    {
        $values = [];

        foreach ($record->fields as $field) {
            $values[] = $field->value->toString();
        }

        return $this->formatInCsv($values);
    }

    /**
     * @param array<string> $values
     */
    private function formatInCsv(array $values): string
    {
        return \implode(',', $values);
    }
}
