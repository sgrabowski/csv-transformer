<?php

namespace App\Application\FieldValue;

use App\Core\Value\FieldValue;

final readonly class EmptyValue implements FieldValue
{
    public function toString(): string
    {
        return '';
    }

    public function internalValue(): null
    {
        return null;
    }
}
