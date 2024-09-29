<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\InputProvider;

use App\Application\Config\InputConfiguration;
use App\Application\Exception\EmptyValuesNotAllowedException;
use App\Application\Factory\FieldValueFactory;
use App\Application\FieldValue\StringValue;
use App\Application\InputProvider\Csv\CsvInputProvider;
use App\Application\InputProvider\Csv\CsvParser;
use App\Core\Value\Record;
use PHPUnit\Framework\TestCase;

class CsvInputProviderTest extends TestCase
{
    private CsvInputProvider $csvInputProvider;
    private CsvParser $csvParser;
    private InputConfiguration $inputConfiguration;
    private FieldValueFactory $fieldValueFactory;

    protected function setUp(): void
    {
        $this->csvParser = $this->createMock(CsvParser::class);
        $this->inputConfiguration = new InputConfiguration();
        $this->fieldValueFactory = new FieldValueFactory();

        $headers = ['group1', 'group2'];
        $this->csvParser->method('getHeaders')->willReturn($headers);

        $this->csvInputProvider = new CsvInputProvider(
            $this->csvParser,
            $this->inputConfiguration,
            $this->fieldValueFactory,
        );
    }

    public function test_next_returns_record_with_valid_fields(): void
    {
        $line = ['value1', 'value2'];
        $this->csvParser->method('nextLine')->willReturn($line);

        $this->inputConfiguration->setInputType('group1', StringValue::class);
        $this->inputConfiguration->setInputType('group2', StringValue::class);

        $record = $this->csvInputProvider->next();

        self::assertInstanceOf(Record::class, $record);
        self::assertCount(2, $record->fields);

        self::assertEquals('group1', $record->fields[0]->groupName);
        self::assertEquals('value1', $record->fields[0]->value->internalValue());

        self::assertEquals('group2', $record->fields[1]->groupName);
        self::assertEquals('value2', $record->fields[1]->value->internalValue());
    }

    public function test_next_skips_discarded_groups(): void
    {
        $line = ['value1', 'value2'];
        $this->csvParser->method('nextLine')->willReturn($line);

        $this->inputConfiguration->setInputType('group1', StringValue::class);
        $this->inputConfiguration->discard('group2');

        $record = $this->csvInputProvider->next();

        self::assertInstanceOf(Record::class, $record);
        self::assertCount(1, $record->fields);  // Only one field should be present since group2 is discarded

        self::assertEquals('group1', $record->fields[0]->groupName);
        self::assertEquals('value1', $record->fields[0]->value->internalValue());
    }

    public function test_next_throws_exception_when_empty_value_not_allowed(): void
    {
        $line = ['', null];
        $this->csvParser->method('nextLine')->willReturn($line);

        $this->inputConfiguration->setInputType('group1', StringValue::class);
        $this->inputConfiguration->setInputType('group2', StringValue::class);
        // Do not allow empty values for group1 (default behavior)

        $this->expectException(EmptyValuesNotAllowedException::class);

        $this->csvInputProvider->next();
    }

    public function test_next_throws_exception_on_field_count_mismatch(): void
    {
        $headers = ['group1', 'group2'];
        $line = ['value1'];  // Only one field provided, but two are expected

        $this->csvParser->method('getHeaders')->willReturn($headers);
        $this->csvParser->method('nextLine')->willReturn($line);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Expected "2" fields, got "1"');

        $this->csvInputProvider->next();
    }

    public function test_next_returns_null_when_no_more_lines(): void
    {
        $this->csvParser->method('getHeaders')->willReturn(['group1']);
        $this->csvParser->method('nextLine')->willReturn(null);  // No more lines

        $result = $this->csvInputProvider->next();

        self::assertNull($result);
    }
}
