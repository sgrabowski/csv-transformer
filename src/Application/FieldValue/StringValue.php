<?php

declare(strict_types=1);

namespace App\Application\FieldValue;

use App\Core\Value\FieldValue;

final readonly class StringValue implements FieldValue
{
    public function __construct(private string $value)
    {
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function internalValue(): string
    {
        return $this->value;
    }
}
