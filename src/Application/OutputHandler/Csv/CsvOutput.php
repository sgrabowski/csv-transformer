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

    /**
     * @return array<string>
     */
    private function extractHeaders(Record $record): array
    {
        $values = [];

        foreach ($record->fields as $field) {
            $values[] = $field->groupName;
        }

        return $values;
    }

    /**
     * @return array<string>
     */
    private function extractValues(Record $record): array
    {
        $values = [];

        foreach ($record->fields as $field) {
            $values[] = $field->value->toString();
        }

        return $values;
    }

    public function finish(): void
    {
        $this->csvWriter->finish();
    }
}
