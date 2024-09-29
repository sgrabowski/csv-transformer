<?php

declare(strict_types=1);

namespace App\Application\FieldValueTransformer\Exception;

use App\Core\Value\FieldValue;

class UnexpectedTypeException extends \Exception
{
    public function __construct(FieldValue $value, string $expectedType)
    {
        parent::__construct(\sprintf('Expected argument of type "%s", "%s" given', $expectedType, $value::class));
    }
}
