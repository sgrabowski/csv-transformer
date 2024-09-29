<?php

namespace App\Tests\Unit\Application\FieldValueTransformer;

use App\Core\Value\FieldValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use App\Application\FieldValue\IntegerValue;
use App\Application\FieldValueTransformer\Exception\UnexpectedTypeException;
use App\Application\FieldValueTransformer\MultiplicationTransformer;

class MultiplicationTransformerTest extends TestCase
{
    #[DataProvider('provideTransformCases')]
    public function testTransform(int $multiplier, int $inputValue, int $expectedResult): void
    {
        $transformer = new MultiplicationTransformer($multiplier);
        $integerValue = new IntegerValue($inputValue);

        $result = $transformer->transform($integerValue);

        self::assertInstanceOf(IntegerValue::class, $result);
        self::assertSame($expectedResult, $result->internalValue());
    }

    public function testTransformThrowsUnexpectedTypeException(): void
    {
        $transformer = new MultiplicationTransformer(2);

        $this->expectException(UnexpectedTypeException::class);

        $transformer->transform($this->createMock(FieldValue::class));
    }

    public static function provideTransformCases(): iterable
    {
        return [
            'multiply by zero' => [0, 10, 0],
            'multiply by positive number' => [5, 2, 10],
            'multiply by negative number' => [-3, 4, -12],
            'multiply negative input by positive multiplier' => [2, -5, -10],
            'multiply two negatives' => [-2, -6, 12],
        ];
    }
}
