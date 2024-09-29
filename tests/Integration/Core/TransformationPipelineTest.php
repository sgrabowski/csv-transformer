<?php

declare(strict_types=1);

namespace App\Tests\Integration\Core;

use App\Core\TransformationPipeline;
use App\Core\Value\Record;
use App\Tests\Doubles\Application\InMemoryInputProvider;
use App\Tests\Doubles\Application\InspectableSinkOutputHandler;
use App\Tests\Doubles\Application\MixedCallbackTransformer;
use App\Tests\Doubles\Application\MixedValue;
use PHPUnit\Framework\TestCase;

class TransformationPipelineTest extends TestCase
{
    private const GROUP_NAME_NEW = 'name uppercase';
    private const GROUP_HEIGHT_NEW = 'height relative to average';

    private TransformationPipeline $pipeline;
    private InspectableSinkOutputHandler $output;

    protected function setUp(): void
    {
        $provider = new InMemoryInputProvider();
        $this->output = new InspectableSinkOutputHandler();

        $this->pipeline = new TransformationPipeline($provider, $this->output);

        $this->pipeline->setFieldValueTransformerForGroup(
            InMemoryInputProvider::GROUP_NAME,
            new MixedCallbackTransformer(static function (MixedValue $value) {
                $upperCased = \strtoupper($value->toString());

                return new MixedValue($upperCased, $upperCased);
            }),
        );

        $this->pipeline->setFieldValueTransformerForGroup(
            InMemoryInputProvider::GROUP_HEIGHT,
            new MixedCallbackTransformer(static function (MixedValue $value) {
                $height = (int) $value->internalValue();
                if ($height > 180) {
                    $newValue = 'above';
                } else {
                    $newValue = 'below';
                }

                return new MixedValue($newValue, $newValue);
            }),
        );

        $this->pipeline->setGroupNameTransformation(InMemoryInputProvider::GROUP_NAME, self::GROUP_NAME_NEW);
        $this->pipeline->setGroupNameTransformation(InMemoryInputProvider::GROUP_HEIGHT, self::GROUP_HEIGHT_NEW);
    }

    public function test_transforms_records(): void
    {
        $this->pipeline->run();

        $firstRecord = $this->output->receiveNextInHandledOrder();
        $secondRecord = $this->output->receiveNextInHandledOrder();
        $nextRecord = $this->output->receiveNextInHandledOrder();

        // first record
        self::assertInstanceOf(Record::class, $firstRecord);
        self::assertCount(2, $firstRecord->fields);
        $field1 = $firstRecord->fields[0];
        $field2 = $firstRecord->fields[1];
        self::assertSame(self::GROUP_NAME_NEW, $field1->groupName);
        self::assertSame('TEST TESTOWSKI', $field1->value->internalValue());
        self::assertSame(self::GROUP_HEIGHT_NEW, $field2->groupName);
        self::assertSame('above', $field2->value->internalValue());

        // second record
        self::assertInstanceOf(Record::class, $secondRecord);
        self::assertCount(2, $secondRecord->fields);
        $field1 = $secondRecord->fields[0];
        $field2 = $secondRecord->fields[1];
        self::assertSame(self::GROUP_NAME_NEW, $field1->groupName);
        self::assertSame('TESTA TESTOWSKA', $field1->value->internalValue());
        self::assertSame(self::GROUP_HEIGHT_NEW, $field2->groupName);
        self::assertSame('below', $field2->value->internalValue());

        // next should be null
        self::assertNull($nextRecord);
    }
}
