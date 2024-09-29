<?php

declare(strict_types=1);

namespace App\Application\FieldValue;

use App\Core\Value\FieldValue;

final readonly class NullValue implements FieldValue
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
