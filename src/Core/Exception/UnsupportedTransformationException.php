<?php

namespace App\Core\Exception;

use App\Core\FieldValueTransformer;
use App\Core\Value\Field;

class UnsupportedTransformationException extends \Exception
{
    public function __construct(Field $field, FieldValueTransformer $transformer)
    {
        parent::__construct(
            \sprintf(
                'Transformer "%s" does not support field values of type "%s"',
                $transformer::class,
                $field->value::class
            ),
        );
    }
}
