<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\OutputHandler\Csv;

use App\Application\FieldValue\StringValue;
use App\Application\OutputHandler\Csv\CsvOutput;
use App\Application\OutputHandler\Csv\CsvWriter;
use App\Core\Value\Field;
use App\Core\Value\Record;
use PHPUnit\Framework\TestCase;

class CsvOutputTest extends TestCase
{
    private CsvWriter $csvWriter;
    private CsvOutput $csvOutput;

    protected function setUp(): void
    {
        $this->csvWriter = $this->createMock(CsvWriter::class);
        $this->csvOutput = new CsvOutput($this->csvWriter);
    }

    public function test_handle_writes_headers_and_data_on_first_record(): void
    {
        $fields = [
            new Field('group1', new StringValue('value1')),
            new Field('group2', new StringValue('value2')),
        ];
        $record = new Record($fields);

        $this->csvWriter->expects(self::once())
            ->method('writeHeaders')
            ->with(['group1', 'group2']);

        $this->csvWriter->expects(self::once())
            ->method('write')
            ->with(['value1', 'value2']);

        $this->csvOutput->handle($record);
    }

    public function test_handle_writes_data_without_headers_if_headers_already_written(): void
    {
        $fields = [
            new Field('group1', new StringValue('value1')),
            new Field('group2', new StringValue('value2')),
        ];
        $record1 = new Record($fields);

        $this->csvOutput->handle($record1);

        $fields2 = [
            new Field('group1', new StringValue('value3')),
            new Field('group2', new StringValue('value4')),
        ];
        $record2 = new Record($fields2);

        $this->csvWriter->expects(self::never())
            ->method('writeHeaders');

        $this->csvWriter->expects(self::once())
            ->method('write')
            ->with(['value3', 'value4']);

        $this->csvOutput->handle($record2);
    }
}
