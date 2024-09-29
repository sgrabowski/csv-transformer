<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Config;

use App\Application\Config\InputConfiguration;
use App\Core\Value\FieldValue;
use App\Tests\Doubles\Application\MixedValue;
use PHPUnit\Framework\TestCase;

class InputConfigurationTest extends TestCase
{
    private InputConfiguration $config;

    protected function setUp(): void
    {
        $this->config = new InputConfiguration();
    }

    public function test_set_input_type_with_valid_class(): void
    {
        $group = 'group1';
        $typeClass = MixedValue::class;

        $this->config->setInputType($group, $typeClass);

        self::assertEquals($typeClass, $this->config->getInputType($group));
    }

    public function test_set_input_type_throws_exception_when_class_does_not_exist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Class "NonExistentClass" does not exist');

        $this->config->setInputType('group1', 'NonExistentClass');
    }

    public function test_set_input_type_throws_exception_when_class_does_not_implement_field_value(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Class "%s" does not implement %s', self::class, FieldValue::class));

        $this->config->setInputType('group1', self::class);
    }

    public function test_allow_null_values(): void
    {
        $group = 'group1';
        $this->config->allowNullValues($group);

        self::assertTrue($this->config->areNullValuesAllowed($group));
    }

    public function test_are_null_values_allowed_returns_false_by_default(): void
    {
        $group = 'group1';

        self::assertFalse($this->config->areNullValuesAllowed($group));
    }

    public function test_discard_group(): void
    {
        $group = 'group1';
        $this->config->discard($group);

        self::assertTrue($this->config->isDiscarded($group));
    }

    public function test_is_discarded_returns_false_by_default(): void
    {
        $group = 'group1';

        self::assertFalse($this->config->isDiscarded($group));
    }
}
