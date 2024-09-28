<?php

namespace App\Tests\Unit\Core;

use App\Core\Exception\InputProviderException;
use App\Core\Exception\OutputHandlerException;
use App\Core\Exception\TransformationException;
use PHPUnit\Framework\TestCase;
use App\Core\TransformationPipeline;
use App\Core\Value\Field;
use App\Core\Value\FieldValue;
use App\Core\Value\Record;
use App\Core\InputProvider;
use App\Core\OutputHandler;
use App\Core\FieldValueTransformer;

final class TransformationPipelineTest extends TestCase
{
    private InputProvider $inputProvider;
    private OutputHandler $outputHandler;
    private FieldValueTransformer $fieldValueTransformer;
    private FieldValue $fieldValue1;
    private FieldValue $fieldValue2;
    private Field $field1;
    private Field $field2;
    private Record $record;

    protected function setUp(): void
    {
        $this->inputProvider = $this->createMock(InputProvider::class);
        $this->outputHandler = $this->createMock(OutputHandler::class);
        $this->fieldValueTransformer = $this->createMock(FieldValueTransformer::class);

        $this->fieldValue1 = $this->createMock(FieldValue::class);
        $this->fieldValue1->method('toString')->willReturn('value1');

        $this->fieldValue2 = $this->createMock(FieldValue::class);
        $this->fieldValue2->method('toString')->willReturn('value2');

        $this->field1 = new Field('group1', $this->fieldValue1);
        $this->field2 = new Field('group2', $this->fieldValue2);

        $this->record = new Record([$this->field1, $this->field2]);
    }

    public function testRunSuccessfullyTransformsRecordsAndPassesToOutputHandler(): void
    {
        $this->inputProvider->expects(self::exactly(2))
            ->method('next')
            ->willReturnOnConsecutiveCalls($this->record, null)
        ;

        $this->outputHandler->expects(self::once())
            ->method('handle')
            ->with(self::callback(function (Record $transformedRecord) {
                $field1 = $transformedRecord->fields[0];
                $field2 = $transformedRecord->fields[1];

                $this->assertSame('group1', $field1->groupName);
                $this->assertSame('new-value', $field1->value->toString());

                $this->assertSame('new-group', $field2->groupName);
                $this->assertSame('value2', $field2->value->toString());

                return true;
            }))
        ;

        $newFieldValue = $this->createMock(FieldValue::class);
        $newFieldValue->method('toString')->willReturn('new-value');

        $this->fieldValueTransformer->expects(self::any())
            ->method('transform')
            ->willReturn($newFieldValue)
        ;

        $pipeline = new TransformationPipeline($this->inputProvider, $this->outputHandler);
        $pipeline->setFieldValueTransformerForGroup('group1', $this->fieldValueTransformer);
        $pipeline->setGroupNameTransformation('group2', 'new-group');

        $pipeline->run();
    }

    public function testRunWrapsTransformationExceptions(): void
    {
        $this->expectException(TransformationException::class);

        $this->inputProvider->expects(self::once())
            ->method('next')
            ->willReturn($this->record)
        ;

        $this->fieldValueTransformer->expects(self::once())
            ->method('transform')
            ->willThrowException(new \Exception('Transformation failed'))
        ;

        $pipeline = new TransformationPipeline($this->inputProvider, $this->outputHandler);
        $pipeline->setFieldValueTransformerForGroup('group1', $this->fieldValueTransformer);

        $pipeline->run();
    }

    public function testRunWrapsOutputHandlerException(): void
    {
        $this->expectException(OutputHandlerException::class);

        $this->inputProvider->expects(self::once())
            ->method('next')
            ->willReturn($this->record)
        ;

        $this->outputHandler->expects(self::once())
            ->method('handle')
            ->willThrowException(new \Exception('Output handling failed'))
        ;

        $pipeline = new TransformationPipeline($this->inputProvider, $this->outputHandler);

        $pipeline->run();
    }

    public function testRunWrapsInputProviderException(): void
    {
        $this->expectException(InputProviderException::class);

        $this->inputProvider->expects(self::once())
            ->method('next')
            ->willReturn($this->record)
        ;

        $this->inputProvider->expects(self::once())
            ->method('next')
            ->willThrowException(new \Exception('Input provider failed'))
        ;

        $pipeline = new TransformationPipeline($this->inputProvider, $this->outputHandler);

        $pipeline->run();
    }
}
