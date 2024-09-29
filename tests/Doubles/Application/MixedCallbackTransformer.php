<?php

declare(strict_types=1);

namespace App\Tests\Doubles\Application;

use App\Core\FieldValueTransformer;
use App\Core\Value\FieldValue;

/**
 * @internal
 */
final readonly class MixedCallbackTransformer implements FieldValueTransformer
{
    private mixed $transformer;

    public function __construct(callable $transformer)
    {
        $this->transformer = $transformer;
    }

    public function transform(FieldValue $value): MixedValue
    {
        return ($this->transformer)($value);
    }

    public function supports(FieldValue $value): bool
    {
        return $value instanceof MixedValue;
    }
}
