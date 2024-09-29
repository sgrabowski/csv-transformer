<?php

declare(strict_types=1);

namespace App\Application\Config;

use App\Core\Value\FieldValue;

final class InputConfiguration
{
    /**
     * @var array<string, string>
     */
    private array $inputTypes = [];

    /**
     * @var array<string>
     */
    private array $groupsWithAllowedNullValues = [];

    /**
     * @var array<string>
     */
    private array $discardedGroups = [];

    public function setInputType(string $group, string $typeClass): void
    {
        if (!\class_exists($typeClass)) {
            throw new \RuntimeException(\sprintf('Class "%s" does not exist', $typeClass));
        }

        if (!\is_subclass_of($typeClass, FieldValue::class)) {
            throw new \RuntimeException(\sprintf('Class "%s" does not implement %s', $typeClass, FieldValue::class));
        }

        $this->inputTypes[$group] = $typeClass;
    }

    public function allowNullValues(string $group): void
    {
        $this->groupsWithAllowedNullValues[] = $group;
    }

    public function discard(string $group): void
    {
        $this->discardedGroups[] = $group;
    }

    public function getInputType(string $group): string
    {
        return $this->inputTypes[$group];
    }

    public function areNullValuesAllowed(string $group): bool
    {
        return \in_array($group, $this->groupsWithAllowedNullValues, true);
    }

    public function isDiscarded(string $group): bool
    {
        return \in_array($group, $this->discardedGroups, true);
    }
}
