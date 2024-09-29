<?php

declare(strict_types=1);

namespace App\Tests\Doubles\Application;

use App\Core\InputProvider;
use App\Core\Value\Field;
use App\Core\Value\Record;

class InMemoryInputProvider implements InputProvider
{
    public const GROUP_NAME = 'name';
    public const GROUP_HEIGHT = 'height';
    public const NAME1 = 'Test Testowski';
    public const NAME2 = 'Testa Testowska';
    public const HEIGHT1 = 193;
    public const HEIGHT2 = 162;

    /**
     * @var array<Record>
     */
    private array $records;
    private \Generator $recordsGenerator;

    public function __construct()
    {
        $this->records = [
            new Record([
                new Field(
                    self::GROUP_NAME,
                    new MixedValue(self::NAME1, self::NAME1),
                ),
                new Field(
                    self::GROUP_HEIGHT,
                    new MixedValue(self::HEIGHT1, '193'),
                ),
            ]),
            new Record([
                new Field(
                    self::GROUP_NAME,
                    new MixedValue(self::NAME2, self::NAME2),
                ),
                new Field(
                    self::GROUP_HEIGHT,
                    new MixedValue(self::HEIGHT2, '162'),
                ),
            ]),
        ];

        $this->recordsGenerator = $this->recordsGenerator();
    }

    public function next(): ?Record
    {
        if ($this->recordsGenerator->valid()) {
            $record = $this->recordsGenerator->current();
            $this->recordsGenerator->next();

            return $record;
        }

        return null;
    }

    private function recordsGenerator(): \Generator
    {
        foreach ($this->records as $record) {
            yield $record;
        }
    }
}
