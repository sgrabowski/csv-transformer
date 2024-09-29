<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\FieldValueTransformer;

use App\Application\FieldValue\NullValue;
use App\Application\FieldValueTransformer\NullAcceptingTransformerAdapter;
use App\Core\FieldValueTransformer;
use App\Core\Value\FieldValue;
use PHPUnit\Framework\TestCase;

class NullAcceptingTransformerAdapterTest extends TestCase
{
    private FieldValueTransformer $wrappedTransformer;
    private NullAcceptingTransformerAdapter $adapter;

    protected function setUp(): void
    {
        $this->wrappedTransformer = $this->createMock(FieldValueTransformer::class);
        $this->adapter = new NullAcceptingTransformerAdapter($this->wrappedTransformer);
    }

    public function test_transform_returns_null_value_when_passed_null_value(): void
    {
        $nullValue = new NullValue();
        $this->wrappedTransformer->expects(self::never())
            ->method('transform');
        $result = $this->adapter->transform($nullValue);

        self::assertSame($nullValue, $result);
    }

    public function test_transform_delegates_to_wrapped_transformer_for_non_null_value(): void
    {
        $nonNullValue = $this->createMock(FieldValue::class);
        $transformedValue = $this->createMock(FieldValue::class);

        $this->wrappedTransformer->expects(self::once())
            ->method('transform')
            ->with($nonNullValue)
            ->willReturn($transformedValue);
        $result = $this->adapter->transform($nonNullValue);

        self::assertSame($transformedValue, $result);
    }

    public function test_supports_returns_true_for_null_value(): void
    {
        $nullValue = new NullValue();

        $this->wrappedTransformer->expects(self::never())
            ->method('supports');
        $result = $this->adapter->supports($nullValue);

        self::assertTrue($result);
    }

    public function test_supports_delegates_to_wrapped_transformer_for_non_null_value(): void
    {
        $nonNullValue = $this->createMock(FieldValue::class);

        $this->wrappedTransformer->expects(self::once())
            ->method('supports')
            ->with($nonNullValue)
            ->willReturn(true);
        $result = $this->adapter->supports($nonNullValue);

        self::assertTrue($result);
    }

    public function test_supports_returns_false_if_wrapped_transformer_does_not_support_non_null_value(): void
    {
        $nonNullValue = $this->createMock(FieldValue::class);

        $this->wrappedTransformer->expects(self::once())
            ->method('supports')
            ->with($nonNullValue)
            ->willReturn(false);
        $result = $this->adapter->supports($nonNullValue);

        self::assertFalse($result);
    }
}
