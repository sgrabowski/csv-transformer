<?php

declare(strict_types=1);

namespace App\Tests\Doubles\Application;

use App\Core\Value\FieldValue;

/**
 * @internal
 */
final readonly class MixedValue implements FieldValue
{
    public function __construct(private mixed $internalValue, private string $stringRepresentation)
    {
    }

    public function toString(): string
    {
        return $this->stringRepresentation;
    }

    public function internalValue(): mixed
    {
        return $this->internalValue;
    }
}
