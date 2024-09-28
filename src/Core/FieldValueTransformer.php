<?php

namespace App\Core;

use App\Core\Value\FieldValue;

interface FieldValueTransformer
{
    public function transform(FieldValue $value): FieldValue;

    public function supports(FieldValue $value): bool;
}
