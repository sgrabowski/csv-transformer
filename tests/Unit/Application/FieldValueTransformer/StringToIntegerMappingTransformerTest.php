<?php

namespace App\Tests\Unit\Application\FieldValueTransformer;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use App\Application\FieldValue\StringValue;
use App\Application\FieldValue\IntegerValue;
use App\Application\FieldValueTransformer\StringToIntegerMappingTransformer;
use App\Application\FieldValueTransformer\Exception\UnexpectedTypeException;
use App\Application\FieldValueTransformer\Exception\UnmappedValueException;
use App\Core\Value\FieldValue;

class StringToIntegerMappingTransformerTest extends TestCase
{
    private StringToIntegerMappingTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new StringToIntegerMappingTransformer();

        $this->transformer->map('one', 1);
        $this->transformer->map('two', 2);
        $this->transformer->map('three', 3);
    }

    #[DataProvider('provideSuccessfulMappingCases')]
    public function testTransformSuccessfullyMapsStringToInteger(string $inputString, int $expectedInteger): void
    {
        $stringValue = new StringValue($inputString);

        $result = $this->transformer->transform($stringValue);

        self::assertInstanceOf(IntegerValue::class, $result);
        self::assertSame($expectedInteger, $result->internalValue());
    }

    public function testTransformThrowsUnexpectedTypeException(): void
    {
        $nonStringValue = $this->createMock(FieldValue::class);

        $this->expectException(UnexpectedTypeException::class);

        $this->transformer->transform($nonStringValue);
    }

    public function testTransformThrowsUnmappedValueException(): void
    {
        $unmappedStringValue = new StringValue('unmapped');

        $this->expectException(UnmappedValueException::class);
        $this->expectExceptionMessage('unmapped');

        $this->transformer->transform($unmappedStringValue);
    }

    public function testAddingNewMappingOverridesExistingMapping(): void
    {
        $stringValue = new StringValue('one');
        $result = $this->transformer->transform($stringValue);
        self::assertSame(1, $result->internalValue());

        $this->transformer->map('one', 10);

        $result = $this->transformer->transform($stringValue);
        self::assertSame(10, $result->internalValue());
    }

    public static function provideSuccessfulMappingCases(): array
    {
        return [
            ['one', 1],
            ['two', 2],
            ['three', 3],
        ];
    }
}
