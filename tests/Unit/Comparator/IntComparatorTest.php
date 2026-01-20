<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\Comparator;

use HrjSnz\SortedLinkedList\Comparator\IntComparator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(IntComparator::class)]
final class IntComparatorTest extends TestCase
{
    private IntComparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new IntComparator();
    }

    #[Test]
    #[DataProvider('provideLessThanCases')]
    #[TestDox('Returns negative when first value is less than second')]
    public function compare_ReturnsNegative_WhenFirstIsLess(int $a, int $b): void
    {
        $result = $this->comparator->compare($a, $b);

        $this->assertLessThan(0, $result);
    }

    /** @return array<string, array{int, int}> */
    public static function provideLessThanCases(): array
    {
        return [
            'small positive numbers' => [5, 10],
            'large positive numbers' => [1000000, 2000000],
            'small negative numbers' => [-10, -5],
            'large negative numbers' => [-2000000, -1000000],
            'negative vs positive' => [-5, 5],
            'zero vs positive' => [0, 5],
            'zero vs negative' => [-5, 0],
            'adjacent values' => [42, 43],
            'PHP_INT_MIN vs zero' => [PHP_INT_MIN, 0],
            'PHP_INT_MIN vs positive' => [PHP_INT_MIN, 100],
        ];
    }

    #[Test]
    #[DataProvider('provideEqualCases')]
    #[TestDox('Returns zero when values are equal')]
    public function compare_ReturnsZero_WhenValuesAreEqual(int $a, int $b): void
    {
        $result = $this->comparator->compare($a, $b);

        $this->assertSame(0, $result);
    }

    /** @return array<string, array{int, int}> */
    public static function provideEqualCases(): array
    {
        return [
            'same positive number' => [7, 7],
            'same negative number' => [-7, -7],
            'both zero' => [0, 0],
            'PHP_INT_MAX' => [PHP_INT_MAX, PHP_INT_MAX],
            'PHP_INT_MIN' => [PHP_INT_MIN, PHP_INT_MIN],
        ];
    }

    #[Test]
    #[DataProvider('provideGreaterThanCases')]
    #[TestDox('Returns positive when first value is greater than second')]
    public function compare_ReturnsPositive_WhenFirstIsGreater(int $a, int $b): void
    {
        $result = $this->comparator->compare($a, $b);

        $this->assertGreaterThan(0, $result);
    }

    /** @return array<string, array{int, int}> */
    public static function provideGreaterThanCases(): array
    {
        return [
            'small positive numbers' => [10, 5],
            'large positive numbers' => [2000000, 1000000],
            'small negative numbers' => [-5, -10],
            'large negative numbers' => [-1000000, -2000000],
            'positive vs negative' => [5, -5],
            'positive vs zero' => [5, 0],
            'zero vs negative' => [0, -5],
            'adjacent values' => [43, 42],
            'zero vs PHP_INT_MIN' => [0, PHP_INT_MIN],
            'positive vs PHP_INT_MIN' => [100, PHP_INT_MIN],
            'PHP_INT_MAX vs large number' => [PHP_INT_MAX, 100],
        ];
    }

    #[Test]
    #[DataProvider('provideInvalidTypes')]
    #[TestDox('Throws InvalidArgumentException when given non-integer')]
    public function compare_ThrowsInvalidArgumentException_WhenGivenNonInt(mixed $a, mixed $b): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Compare only integers');

        $this->comparator->compare($a, $b);
    }

    /** @return array<string, array{mixed, mixed}> */
    public static function provideInvalidTypes(): array
    {
        return [
            'float vs int' => [1.5, 5],
            'int vs float' => [5, 1.5],
            'string vs int' => ['5', 5],
            'int vs string' => [5, '5'],
            'null vs int' => [null, 5],
            'array vs int' => [[], 5],
            'object vs int' => [new \stdClass(), 5],
            'bool vs int' => [true, 5],
            'int vs bool' => [5, false],
        ];
    }
}
