<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Application\FieldValue\DateTimeValue;
use App\Application\FieldValue\NullValue;
use App\Application\FieldValue\NumericValue;
use App\Application\FieldValue\StringValue;
use App\Core\Value\FieldValue;

final readonly class FieldValueFactory
{
    public function create(string $fieldType, mixed $value): FieldValue
    {
        if (!\class_exists($fieldType)) {
            throw new \RuntimeException(\sprintf('Class "%s" does not exist', $fieldType));
        }

        if (!\is_subclass_of($fieldType, FieldValue::class)) {
            throw new \RuntimeException(\sprintf('Class "%s" does not implement %s', $fieldType, FieldValue::class));
        }

        if ($value === null) {
            return new NullValue();
        }

        switch ($fieldType) {
            case StringValue::class:
                return new StringValue($value);
            case NumericValue::class:
                return new NumericValue($value);
            case DateTimeValue::class:
                return new DateTimeValue(new \DateTimeImmutable($value));
        }

        throw new \RuntimeException(\sprintf('Field type "%s" is not set up in the factory', $fieldType));
    }
}
