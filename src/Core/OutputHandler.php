<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Value\Record;

interface OutputHandler
{
    public function handle(Record $record): void;
}
