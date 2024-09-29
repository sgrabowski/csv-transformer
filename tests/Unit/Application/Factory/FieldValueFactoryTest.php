<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Factory;

use App\Application\Factory\FieldValueFactory;
use App\Application\FieldValue\DateTimeValue;
use App\Application\FieldValue\NullValue;
use App\Application\FieldValue\NumericValue;
use App\Application\FieldValue\StringValue;
use App\Core\Value\FieldValue;
use App\Tests\Doubles\Application\MixedValue;
use PHPUnit\Framework\TestCase;

class FieldValueFactoryTest extends TestCase
{
    public function test_create_with_valid_string_value(): void
    {
        $value = 'Test string';
        $result = (new FieldValueFactory())->create(StringValue::class, $value);
        self::assertInstanceOf(StringValue::class, $result);
        self::assertEquals($value, $result->internalValue());
    }

    public function test_create_with_integer_value_creates_numeric_value(): void
    {
        $value = 123;
        $result = (new FieldValueFactory())->create(NumericValue::class, $value);
        self::assertInstanceOf(NumericValue::class, $result);
        self::assertEquals($value, $result->internalValue());
    }

    public function test_create_with_float_value_creates_numeric_value(): void
    {
        $value = 1.23;
        $result = (new FieldValueFactory())->create(NumericValue::class, $value);
        self::assertInstanceOf(NumericValue::class, $result);
        self::assertEquals($value, $result->internalValue());
    }

    public function test_create_with_valid_date_time_value(): void
    {
        $value = '2024-01-01 12:00:00';
        $result = (new FieldValueFactory())->create(DateTimeValue::class, $value);
        self::assertInstanceOf(DateTimeValue::class, $result);
        self::assertEquals($value, $result->internalValue()->format('Y-m-d H:i:s'));
    }

    public function test_create_with_null_value_returns_empty_value_instance(): void
    {
        $result = (new FieldValueFactory())->create(NumericValue::class, null);
        self::assertInstanceOf(NullValue::class, $result);
    }

    public function test_create_with_empty_string_value_returns_string_instance(): void
    {
        $result = (new FieldValueFactory())->create(StringValue::class, '');
        self::assertInstanceOf(StringValue::class, $result);
    }

    public function test_create_throws_exception_when_class_does_not_exist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Class "NonExistentClass" does not exist');

        (new FieldValueFactory())->create('NonExistentClass', 'some value');
    }

    public function test_create_throws_exception_when_class_does_not_implement_field_value(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Class "%s" does not implement %s', self::class, FieldValue::class));

        (new FieldValueFactory())->create(self::class, 'some value');
    }

    public function test_create_throws_exception_when_field_type_not_set_up_in_factory(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Field type "%s" is not set up in the factory', MixedValue::class));

        (new FieldValueFactory())->create(MixedValue::class, 'some value');
    }
}
