<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use App\Application\Builder\CsvInputPipelineBuilder;
use App\Application\FieldValue\DateTimeValue;
use App\Application\FieldValue\NumericValue;
use App\Application\FieldValue\StringValue;
use App\Application\InputProvider\Csv\CsvParser;
use App\Application\OutputHandler\Csv\CsvWriter;
use App\Core\TransformationPipeline;

class DefaultPipelineConfig
{
    public function getDefaultPipeline(
        CsvParser $csvParser,
        CsvWriter $csvWriter,
    ): TransformationPipeline {

        $csvWriter->setDelimiter(';');
        $csvWriter->setDelimiter(';');

        return CsvInputPipelineBuilder::new()
            ->setCsvParser($csvParser)
            ->setCsvWriter($csvWriter)
            ->forColumn('Patient ID')
                ->setExpectedType(NumericValue::class)
                ->changeName('record_id')
            ->forColumn('Name')
                ->discard()
            ->forColumn('Gender')
                ->setExpectedType(StringValue::class)
                ->changeName('gender')
                ->mapStringsToIntegers([
                    'Male' => 1,
                    'Female' => 2,
                ])
            ->forColumn('Length')
                ->setExpectedType(NumericValue::class)
                ->changeName('height_cm')
                ->multiply(100)
            ->forColumn('Weight')
                ->setExpectedType(NumericValue::class)
                ->changeName('weight_kg')
            ->forColumn('Pregnant')
                ->setExpectedType(StringValue::class)
                ->changeName('pregnant')
                ->mapStringsToIntegers([
                    'Yes' => 1,
                    'No' => 0,
                ])
            ->forColumn('Months Pregnant')
                ->setExpectedType(NumericValue::class)
                ->changeName('pregnancy_duration_weeks')
                ->multiply(4)
                ->allowNulls()
            ->forColumn('Date of diagnosis')
                ->setExpectedType(DateTimeValue::class)
                ->changeName('date_diagnosis')
                ->changeDateFormatTo('Y-m-d')
            ->build();
    }
}
