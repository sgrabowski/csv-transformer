<?php

declare(strict_types=1);

namespace App\Application\FieldValue;

use App\Core\Value\FieldValue;

final readonly class IntegerValue implements FieldValue
{
    public function __construct(private int $value)
    {
    }

    public function toString(): string
    {
        return (string) $this->value;
    }

    public function internalValue(): int
    {
        return $this->value;
    }
}
