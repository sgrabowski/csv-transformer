<?php

declare(strict_types=1);

namespace App\Core\Value;

interface FieldValue
{
    public function toString(): string;

    public function internalValue(): mixed;
}
