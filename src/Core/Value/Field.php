<?php

namespace App\Core\Value;

final readonly class Field
{
    public function __construct(
        /**
         * Used for grouping fields together.
         * This will usually be the column name in a dataset.
         */
        public string $groupName,
        public FieldValue $value,
    ) {
    }
}
