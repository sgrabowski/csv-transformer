<?php

declare(strict_types=1);

namespace App\Application\InputProvider\Csv;

use App\Application\Config\InputConfiguration;
use App\Application\Exception\EmptyValuesNotAllowedException;
use App\Application\Factory\FieldValueFactory;
use App\Application\FieldValue\NullValue;
use App\Core\InputProvider;
use App\Core\Value\Field;
use App\Core\Value\Record;

readonly class CsvInputProvider implements InputProvider
{
    /**
     * @var array<string>
     */
    private array $groups;

    private int $fieldCount;

    public function __construct(
        private CsvParser $csvParser,
        private InputConfiguration $inputConfiguration,
        private FieldValueFactory $fieldValueFactory,
    ) {
        $this->groups = $this->csvParser->getHeaders();
        $this->fieldCount = \count($this->groups);
    }

    /**
     * @throws EmptyValuesNotAllowedException
     */
    public function next(): ?Record
    {
        $fields = [];
        $line = $this->csvParser->nextLine();

        if ($line === null) {
            return null;
        }


        if (\count($line) !== $this->fieldCount) {
            throw new \RuntimeException(
                \sprintf(
                    'Expected "%s" fields, got "%s" in the following CSV line: "%s"',
                    $this->fieldCount,
                    \count($line),
                    \implode(', ', $line),
                ),
            );
        }

        foreach ($this->groups as $index => $group) {
            if ($this->inputConfiguration->isDiscarded($group)) {
                continue;
            }

            $rawValue = $line[$index];
            $fieldValue = $this->fieldValueFactory->create($this->inputConfiguration->getInputType($group), $rawValue);

            if ($fieldValue instanceof NullValue && !$this->inputConfiguration->areNullValuesAllowed($group)) {
                throw new EmptyValuesNotAllowedException($group);
            }

            $fields[] = new Field($group, $fieldValue);
        }

        return new Record($fields);
    }
}
