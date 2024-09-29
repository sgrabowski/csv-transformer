<?php

namespace App\Application\FieldValue;

use App\Core\Value\FieldValue;

final readonly class DateTimeValue implements FieldValue
{
    public function __construct(private \DateTimeImmutable $value)
    {
    }

    public function toString(): string
    {
        return $this->value->format('Y-m-d');
    }

    public function internalValue(): \DateTimeImmutable
    {
        return $this->value;
    }
}
