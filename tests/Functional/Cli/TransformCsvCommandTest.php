<?php

declare(strict_types=1);

namespace App\Tests\Functional\Cli;

use App\Cli\TransformCsvCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class TransformCsvCommandTest extends KernelTestCase
{
    public function test_process_csv_command(): void
    {
        self::bootKernel();
        $filesystem = new Filesystem();

        $projectDir = self::$kernel->getProjectDir();
        $inputCsv = $projectDir . '/data/input.csv';
        $expectedOutputCsv = $projectDir . '/data/output.csv';

        $tempOutputFile = $projectDir . '/data/output_test.csv';

        if ($filesystem->exists($tempOutputFile)) {
            $filesystem->remove($tempOutputFile);
        }

        try {
            $application = new Application(self::$kernel);
            $application->add(new TransformCsvCommand());
            $command = $application->find('app:transform:csv');

            $commandTester = new CommandTester($command);
            $commandTester->execute([
                'input_csv' => $inputCsv,
                'output_csv' => $tempOutputFile,
            ]);

            self::assertEquals(
                Command::SUCCESS,
                $commandTester->getStatusCode(),
                'Command exited with a non-zero status code.',
            );

            self::assertFileEquals(
                $expectedOutputCsv,
                $tempOutputFile,
                'The output CSV file does not match the expected output.',
            );
        } finally {
            if ($filesystem->exists($tempOutputFile)) {
                $filesystem->remove($tempOutputFile);
            }
        }
    }

    // https://github.com/symfony/symfony/issues/53812#issuecomment-1958859357
    protected function restoreExceptionHandler(): void
    {
        while (true) {
            $previousHandler = \set_exception_handler(static fn () => null);

            \restore_exception_handler();

            if ($previousHandler === null) {
                break;
            }

            \restore_exception_handler();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->restoreExceptionHandler();
    }
}
