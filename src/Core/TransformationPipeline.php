<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exception\InputProviderException;
use App\Core\Exception\OutputHandlerException;
use App\Core\Exception\TransformationException;
use App\Core\Exception\UnsupportedTransformationException;
use App\Core\Value\Field;
use App\Core\Value\Record;

final class TransformationPipeline
{
    /**
     * @var array<FieldValueTransformer>
     */
    private array $fieldValueTransformers = [];

    /**
     * @var array<string>
     */
    private array $groupTransformationSet = [];

    public function __construct(
        private readonly InputProvider $inputProvider,
        private readonly OutputHandler $outputHandler,
    ) {
    }

    /**
     * @throws InputProviderException
     * @throws OutputHandlerException
     * @throws TransformationException
     */
    public function run(): void
    {
        while (($record = $this->pullNextRecordFromInputProvider()) !== null) {
            $transformedRecord = $this->transformRecord($record);

            try {
                $this->outputHandler->handle($transformedRecord);
            } catch (\Throwable $exception) {
                throw new OutputHandlerException(previous: $exception);
            }
        }
    }

    /**
     * Group value transformation will be applied before group name transformation.
     */
    public function setFieldValueTransformerForGroup(string $group, FieldValueTransformer $fieldValueTransformer): void
    {
        $this->fieldValueTransformers[$group] = $fieldValueTransformer;
    }

    /**
     * Group name transformation will be applied after value transformation.
     */
    public function setGroupNameTransformation(string $original, string $new): void
    {
        $this->groupTransformationSet[$original] = $new;
    }

    /**
     * @throws TransformationException
     */
    private function transformRecord(Record $record): Record
    {
        $transformedFields = [];

        foreach ($record->fields as $field) {
            try {
                $transformedFields[] = $this->transformField($field);
            } catch (\Throwable $exception) {
                throw new TransformationException($field, $exception);
            }
        }

        return new Record($transformedFields);
    }

    /**
     * @throws UnsupportedTransformationException
     */
    private function transformField(Field $field): Field
    {
        $newValue = $field->value;
        if ($this->hasValueTransformer($field)) {
            $transformer = $this->fieldValueTransformers[$field->groupName];

            if (!$transformer->supports($field->value)) {
                throw new UnsupportedTransformationException($field, $transformer);
            }

            $newValue = $transformer->transform($field->value);
        }

        $newGroupName = $this->groupTransformationSet[$field->groupName] ?? $field->groupName;

        return new Field($newGroupName, $newValue);
    }

    private function hasValueTransformer(Field $field): bool
    {
        return \array_key_exists($field->groupName, $this->fieldValueTransformers);
    }

    /**
     * @throws InputProviderException
     */
    private function pullNextRecordFromInputProvider(): ?Record
    {
        try {
            return $this->inputProvider->next();
        } catch (\Throwable $exception) {
            throw new InputProviderException(previous: $exception);
        }
    }
}
