<?php

namespace App\Tests\Unit\Application\FieldValueTransformer;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use App\Application\FieldValue\DateTimeValue;
use App\Application\FieldValue\StringValue;
use App\Application\FieldValueTransformer\DateTimeFormatTransformer;
use App\Application\FieldValueTransformer\Exception\UnexpectedTypeException;
use App\Core\Value\FieldValue;

class DateTimeFormatTransformerTest extends TestCase
{
    #[DataProvider('provideTransformCases')]
    public function testTransform(\DateTimeImmutable $dateTime, string $targetFormat, string $expectedResult): void
    {
        $transformer = new DateTimeFormatTransformer($targetFormat);
        $dateTimeValue = new DateTimeValue($dateTime);

        $result = $transformer->transform($dateTimeValue);

        self::assertInstanceOf(StringValue::class, $result);
        self::assertSame($expectedResult, $result->internalValue());
    }

    public function testTransformThrowsUnexpectedTypeException(): void
    {
        $transformer = new DateTimeFormatTransformer('Y-m-d');

        $this->expectException(UnexpectedTypeException::class);

        $transformer->transform($this->createMock(FieldValue::class));
    }

    public static function provideTransformCases(): array
    {
        return [
            'basic date format' => [
                new \DateTimeImmutable('2023-01-01'),
                'Y-m-d',
                '2023-01-01',
            ],
            'full date and time format' => [
                new \DateTimeImmutable('2023-01-01 10:30:45'),
                'Y-m-d H:i:s',
                '2023-01-01 10:30:45',
            ],
            'custom format with month name' => [
                new \DateTimeImmutable('2023-05-15 08:45:00'),
                'F j, Y',
                'May 15, 2023',
            ],
            'time only format' => [
                new \DateTimeImmutable('2023-05-15 08:45:00'),
                'H:i:s',
                '08:45:00',
            ],
        ];
    }
}
