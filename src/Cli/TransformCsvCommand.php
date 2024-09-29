<?php

declare(strict_types=1);

namespace App\Cli;

use App\Infrastructure\Config\DefaultPipelineConfig;
use App\Infrastructure\Csv\CsvFileParser;
use App\Infrastructure\Csv\CsvFileWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TransformCsvCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('app:transform:csv')
            ->setDescription('Processes an input CSV file and outputs the result to another CSV file.')
            ->setHelp(
                // phpcs:disable
                <<<'HELP'
                    The <info>%command.name%</info> command processes an input CSV file and writes the output to another CSV file.

                    Usage:

                      <info>php %command.full_name% input.csv output.csv</info>

                    Arguments:

                      <info>input.csv</info>   The path to the input CSV file.
                      <info>output.csv</info>  The path where the output CSV file will be saved.
                    HELP
                // phpcs:enable
            )
            ->addArgument(
                'input_csv',
                InputArgument::REQUIRED,
                'The path to the input CSV file.',
            )
            ->addArgument(
                'output_csv',
                InputArgument::REQUIRED,
                'The path where the output CSV file will be saved.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputCsvPath = $input->getArgument('input_csv');
        $outputCsvPath = $input->getArgument('output_csv');

        // Display informational messages
        $output->writeln('<info>Processing CSV file...</info>');
        $output->writeln("Input CSV: <comment>{$inputCsvPath}</comment>");
        $output->writeln("Output CSV: <comment>{$outputCsvPath}</comment>");

        try {
            $csvParser = new CsvFileParser($inputCsvPath, ';'); // delimiter should be an input option ideally
            $csvWriter = new CsvFileWriter($outputCsvPath);

            (new DefaultPipelineConfig())->getDefaultPipeline($csvParser, $csvWriter)->run();

            $output->writeln('<info>CSV processing completed successfully.</info>');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>An error occurred during CSV processing:</error>');
            $output->writeln('<error>' . $e::class . ':' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }
    }
}
