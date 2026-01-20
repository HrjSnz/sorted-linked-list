<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\Comparator;

use HrjSnz\SortedLinkedList\Comparator\IntComparator;
use HrjSnz\SortedLinkedList\Comparator\ReverseComparator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReverseComparator::class)]
final class ReverseComparatorTest extends TestCase
{
    #[Test]
    #[DataProvider('provideReversedLessThanCases')]
    #[TestDox('Reverses comparison: positive when inner returns negative')]
    public function compare_WithIntComparator_ReturnsPositive_WhenInnerReturnsNegative(int $a, int $b): void
    {
        $inner = new IntComparator();
        $comparator = new ReverseComparator($inner);

        $result = $comparator->compare($a, $b);

        $this->assertGreaterThan(0, $result);
    }

    /** @return array<string, array{int, int}> */
    public static function provideReversedLessThanCases(): array
    {
        return [
            '5 vs 10' => [5, 10],
            '-10 vs -5' => [-10, -5],
            'negative vs positive' => [-5, 5],
            '0 vs 5' => [0, 5],
        ];
    }

    #[Test]
    #[DataProvider('provideEqualCases')]
    #[TestDox('Preserves equality: zero when inner returns zero')]
    public function compare_WithIntComparator_ReturnsZero_WhenValuesAreEqual(int $a, int $b): void
    {
        $inner = new IntComparator();
        $comparator = new ReverseComparator($inner);

        $result = $comparator->compare($a, $b);

        $this->assertSame(0, $result);
    }

    /** @return array<string, array{int, int}> */
    public static function provideEqualCases(): array
    {
        return [
            'same number' => [7, 7],
            'zero' => [0, 0],
            'same negative' => [-5, -5],
        ];
    }

    #[Test]
    #[DataProvider('provideReversedGreaterThanCases')]
    #[TestDox('Reverses comparison: negative when inner returns positive')]
    public function compare_WithIntComparator_ReturnsNegative_WhenInnerReturnsPositive(int $a, int $b): void
    {
        $inner = new IntComparator();
        $comparator = new ReverseComparator($inner);

        $result = $comparator->compare($a, $b);

        $this->assertLessThan(0, $result);
    }

    /** @return array<string, array{int, int}> */
    public static function provideReversedGreaterThanCases(): array
    {
        return [
            '10 vs 5' => [10, 5],
            '-5 vs -10' => [-5, -10],
            'positive vs negative' => [5, -5],
            '5 vs 0' => [5, 0],
        ];
    }

    #[Test]
    #[TestDox('Reverses StringComparator comparisons correctly')]
    public function compare_WithStringComparator_ReversesComparison(): void
    {
        $inner = new \HrjSnz\SortedLinkedList\Comparator\StringComparator();
        $comparator = new ReverseComparator($inner);

        $result = $comparator->compare('apple', 'banana');
        $this->assertGreaterThan(0, $result);

        $result = $comparator->compare('test', 'test');
        $this->assertSame(0, $result);

        $result = $comparator->compare('zebra', 'apple');
        $this->assertLessThan(0, $result);
    }

    #[Test]
    #[DataProvider('provideDoubleReversalCases')]
    #[TestDox('Double reversal returns original comparison')]
    public function compare_DoubleReversal_ReturnsOriginalComparison(int $a, int $b): void
    {
        $inner = new IntComparator();
        $singleReverse = new ReverseComparator($inner);
        $doubleReverse = new ReverseComparator($singleReverse);

        $originalResult = $inner->compare($a, $b);
        $doubleReverseResult = $doubleReverse->compare($a, $b);

        $this->assertSame($originalResult, $doubleReverseResult);
    }

    /** @return array<string, array{int, int}> */
    public static function provideDoubleReversalCases(): array
    {
        return [
            'less than' => [5, 10],
            'equal' => [7, 7],
            'greater than' => [10, 5],
            'mixed signs' => [-5, 5],
        ];
    }
}
