<?php

declare(strict_types=1);

namespace App\Application\FieldValue;

use App\Core\Value\FieldValue;

final readonly class NumericValue implements FieldValue
{
    public function __construct(private int | float $value)
    {
    }

    public function toString(): string
    {
        return (string) $this->value;
    }

    public function internalValue(): int | float
    {
        return $this->value;
    }
}
