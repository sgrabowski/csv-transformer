<?php

namespace App\Core;

use App\Core\Value\Record;

interface InputProvider
{
    /**
     * Providers the next record to process. If no more records are available, returns null.
     */
    public function next(): ?Record;
}
