<?php

declare(strict_types=1);

namespace App\Application\Exception;

class EmptyValuesNotAllowedException extends \Exception
{
    public function __construct(string $group)
    {
        parent::__construct(
            \sprintf('Empty values allowed for group "%s"', $group),
        );
    }
}
