<?php

namespace App\Core\Exception;

use App\Core\Value\Field;

final class TransformationException extends \Exception
{
    public function __construct(Field $field, ?\Throwable $previous = null)
    {
        parent::__construct(
            \sprintf('Failed to transform field "%s" with value "%s"', Field::class, $field->value->toString()),
            0,
            $previous
        );
    }
}
