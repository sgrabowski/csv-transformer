<?php

declare(strict_types=1);

namespace App\Application\FieldValueTransformer;

use App\Application\FieldValue\NumericValue;
use App\Application\FieldValueTransformer\Exception\UnexpectedTypeException;
use App\Core\FieldValueTransformer;
use App\Core\Value\FieldValue;

final readonly class MultiplicationTransformer implements FieldValueTransformer
{
    public function __construct(private int $multiplier)
    {
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function transform(FieldValue $value): NumericValue
    {
        if (!$value instanceof NumericValue) {
            throw new UnexpectedTypeException($value, NumericValue::class);
        }

        return new NumericValue($value->internalValue() * $this->multiplier);
    }

    public function supports(FieldValue $value): bool
    {
        return $value instanceof NumericValue;
    }
}
