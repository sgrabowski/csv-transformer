<?php

declare(strict_types=1);

namespace App\Application\FieldValueTransformer;

use App\Application\FieldValue\DateTimeValue;
use App\Application\FieldValue\StringValue;
use App\Application\FieldValueTransformer\Exception\UnexpectedTypeException;
use App\Core\FieldValueTransformer;
use App\Core\Value\FieldValue;

final readonly class DateTimeFormatTransformer implements FieldValueTransformer
{
    public function __construct(private string $targetFormat)
    {
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function transform(FieldValue $value): StringValue
    {
        if (!$value instanceof DateTimeValue) {
            throw new UnexpectedTypeException($value, DateTimeValue::class);
        }

        return new StringValue($value->internalValue()->format($this->targetFormat));
    }

    public function supports(FieldValue $value): bool
    {
        return $value instanceof DateTimeValue;
    }
}
