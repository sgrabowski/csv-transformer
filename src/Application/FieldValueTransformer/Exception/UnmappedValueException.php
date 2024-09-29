<?php

namespace App\Application\FieldValueTransformer\Exception;

class UnmappedValueException extends \Exception
{
    public function __construct(mixed $value)
    {
        parent::__construct(
            \sprintf('Value "%s" of type "%s" is not mapped', $value, \gettype($value)),
        );
    }
}
