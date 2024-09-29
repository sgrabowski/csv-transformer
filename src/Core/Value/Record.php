<?php

declare(strict_types=1);

namespace App\Core\Value;

final readonly class Record
{
    /**
     * @var array<Field>
     */
    public array $fields;

    /**
     * @param Field[] $fields an array of Field objects
     *
     * @throws \InvalidArgumentException if any element is not a Field instance
     */
    public function __construct(array $fields)
    {
        foreach ($fields as $field) {
            if (!$field instanceof Field) {
                throw new \InvalidArgumentException('All elements of $fields must be instances of Field.');
            }
        }

        $this->fields = $fields;
    }
}
