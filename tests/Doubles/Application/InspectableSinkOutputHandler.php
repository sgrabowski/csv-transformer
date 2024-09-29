<?php

declare(strict_types=1);

namespace App\Tests\Doubles\Application;

use App\Core\OutputHandler;
use App\Core\Value\Record;

class InspectableSinkOutputHandler implements OutputHandler
{
    /**
     * @var array<Record>
     */
    private array $sink = [];

    public function handle(Record $record): void
    {
        $this->sink[] = $record;
    }

    public function receiveNextInHandledOrder(): ?Record
    {
        return \array_shift($this->sink);
    }
}
