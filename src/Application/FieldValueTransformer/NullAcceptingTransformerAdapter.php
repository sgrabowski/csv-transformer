<?php

declare(strict_types=1);

namespace App\Application\FieldValueTransformer;

use App\Application\FieldValue\NullValue;
use App\Core\FieldValueTransformer;
use App\Core\Value\FieldValue;

final readonly class NullAcceptingTransformerAdapter implements FieldValueTransformer
{
    public function __construct(private FieldValueTransformer $wrappedTransformer)
    {
    }

    public function transform(FieldValue $value): FieldValue
    {
        if ($value instanceof NullValue) {
            return $value;
        }

        return $this->wrappedTransformer->transform($value);
    }

    public function supports(FieldValue $value): bool
    {
        return $value instanceof NullValue || $this->wrappedTransformer->supports($value);
    }
}
