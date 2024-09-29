<?php

namespace App\Application\FieldValueTransformer;

use App\Application\FieldValue\IntegerValue;
use App\Application\FieldValue\StringValue;
use App\Application\FieldValueTransformer\Exception\UnexpectedTypeException;
use App\Application\FieldValueTransformer\Exception\UnmappedValueException;
use App\Core\FieldValueTransformer;
use App\Core\Value\FieldValue;

final class StringToIntegerMappingTransformer implements FieldValueTransformer
{
    /**
     * @var array<string, int>
     */
    private array $mapping = [];

    /**
     * @throws UnexpectedTypeException
     * @throws UnmappedValueException
     */
    public function transform(FieldValue $value): IntegerValue
    {
        if (!$value instanceof StringValue) {
            throw new UnexpectedTypeException($value, StringValue::class);
        }

        $key = $value->toString();
        if (!$this->mappingExists($key)) {
            throw new UnmappedValueException($key);
        }

        return new IntegerValue($this->mapping[$key]);
    }

    public function supports(FieldValue $value): bool
    {
        return $value instanceof StringValue;
    }

    private function mappingExists(string $key): bool
    {
        return \array_key_exists($key, $this->mapping);
    }

    public function map(string $key, int $value): void
    {
        $this->mapping[$key] = $value;
    }
}
