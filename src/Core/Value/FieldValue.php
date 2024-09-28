<?php

namespace App\Core\Value;

interface FieldValue
{
    public function toString(): string;

    public static function fromString(string $stringRepresentation): self;
}
