<?php

declare(strict_types=1);

namespace App\Application\Builder;

use App\Application\Config\InputConfiguration;
use App\Application\Exception\IncompletePipelineConfiguration;
use App\Application\Factory\FieldValueFactory;
use App\Application\FieldValueTransformer\DateTimeFormatTransformer;
use App\Application\FieldValueTransformer\MultiplicationTransformer;
use App\Application\FieldValueTransformer\NullAcceptingTransformerAdapter;
use App\Application\FieldValueTransformer\StringToIntegerMappingTransformer;
use App\Application\InputProvider\Csv\CsvInputProvider;
use App\Application\InputProvider\Csv\CsvParser;
use App\Application\OutputHandler\Csv\CsvOutput;
use App\Application\OutputHandler\Csv\CsvWriter;
use App\Core\FieldValueTransformer;
use App\Core\TransformationPipeline;

final class CsvInputPipelineBuilder
{
    private ?CsvParser $csvParser = null;
    private ?CsvWriter $csvWriter = null;
    private InputConfiguration $inputConfiguration;
    private ?string $builderContext = null;

    /**
     * @var array<string>
     */
    private array $groupNameTransformations = [];

    /**
     * @var array<FieldValueTransformer>
     */
    private array $fieldTransformations = [];

    private function __construct()
    {
        $this->inputConfiguration = new InputConfiguration();
    }

    public static function new(): self
    {
        return new self();
    }

    /**
     * @throws IncompletePipelineConfiguration
     */
    public function build(): TransformationPipeline
    {
        $this->throwOnMissingRequirements();

        $pipeline = new TransformationPipeline(
            new CsvInputProvider($this->csvParser, $this->inputConfiguration, new FieldValueFactory()),
            new CsvOutput($this->csvWriter),
        );

        foreach ($this->groupNameTransformations as $groupName => $targetName) {
            $pipeline->setGroupNameTransformation($groupName, $targetName);
        }

        foreach ($this->fieldTransformations as $groupName => $transformer) {
            if ($this->inputConfiguration->areNullValuesAllowed($groupName)) {
                $transformer = new NullAcceptingTransformerAdapter($transformer);
            }

            $pipeline->setFieldValueTransformerForGroup($groupName, $transformer);
        }

        return $pipeline;
    }

    public function forColumn(string $columnName): self
    {
        $this->builderContext = $columnName;

        return $this;
    }

    public function discard(): self
    {
        $this->throwOnMissingContext();

        $this->inputConfiguration->discard($this->builderContext);
        $this->builderContext = null; // no need to further adjust discarded columns

        return $this;
    }

    public function changeName(string $newName): self
    {
        $this->throwOnMissingContext();

        $this->groupNameTransformations[$this->builderContext] = $newName;

        return $this;
    }

    public function setExpectedType(string $expectedType): self
    {
        $this->throwOnMissingContext();

        $this->inputConfiguration->setInputType($this->builderContext, $expectedType);

        return $this;
    }

    public function allowNulls(): self
    {
        $this->throwOnMissingContext();

        $this->inputConfiguration->allowNullValues($this->builderContext);

        return $this;
    }

    public function changeDateFormatTo(string $newFormat): self
    {
        $this->throwOnMissingContext();

        $this->fieldTransformations[$this->builderContext] = new DateTimeFormatTransformer($newFormat);

        return $this;
    }

    public function multiply(int $multiplier): self
    {
        $this->throwOnMissingContext();

        $this->fieldTransformations[$this->builderContext] = new MultiplicationTransformer($multiplier);

        return $this;
    }

    /**
     * @param array<string, int> $map
     */
    public function mapStringsToIntegers(array $map): self
    {
        $this->throwOnMissingContext();

        $transformer = new StringToIntegerMappingTransformer();

        foreach ($map as $key => $value) {
            $transformer->map($key, $value);
        }

        $this->fieldTransformations[$this->builderContext] = $transformer;

        return $this;
    }

    public function setCsvParser(CsvParser $csvParser): self
    {
        $this->csvParser = $csvParser;

        return $this;
    }

    public function setCsvWriter(CsvWriter $csvWriter): self
    {
        $this->csvWriter = $csvWriter;

        return $this;
    }

    private function throwOnMissingRequirements(): void
    {
        if ($this->csvParser === null) {
            throw new IncompletePipelineConfiguration('You must provide a csv parser');
        }

        if ($this->csvWriter === null) {
            throw new IncompletePipelineConfiguration('You must define a csv writer');
        }
    }

    private function throwOnMissingContext(): void
    {
        if ($this->builderContext === null) {
            throw new IncompletePipelineConfiguration(
                'You must define a builder context by using the "forColumn" method',
            );
        }
    }
}
