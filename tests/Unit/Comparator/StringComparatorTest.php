<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\Comparator;

use HrjSnz\SortedLinkedList\Comparator\StringComparator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(StringComparator::class)]
final class StringComparatorTest extends TestCase
{
    private StringComparator $comparator;
    private StringComparator $caseInsensitiveComparator;

    protected function setUp(): void
    {
        $this->comparator = new StringComparator(caseSensitive: true);
        $this->caseInsensitiveComparator = new StringComparator(caseSensitive: false);
    }

    #[Test]
    #[DataProvider('provideLessThanCases')]
    #[TestDox('Returns negative when first string is less than second (case-sensitive)')]
    public function compare_ReturnsNegative_WhenFirstIsLess(string $a, string $b): void
    {
        $result = $this->comparator->compare($a, $b);

        $this->assertLessThan(0, $result);
    }

    /** @return array<string, array{string, string}> */
    public static function provideLessThanCases(): array
    {
        return [
            'alphabetical order' => ['apple', 'banana'],
            'shorter before longer' => ['app', 'apple'],
            'single char' => ['a', 'b'],
            'uppercase before lowercase' => ['Apple', 'apple'],
            'all caps' => ['APPLE', 'BANANA'],
            'with numbers' => ['a1', 'a2'],
            'special chars' => ['a', 'b'],
            'empty string first' => ['', 'text'],
            'space first' => [' ', 'a'],
        ];
    }

    #[Test]
    #[DataProvider('provideEqualCases')]
    #[TestDox('Returns zero when strings are equal (case-sensitive)')]
    public function compare_ReturnsZero_WhenStringsAreEqual(string $a, string $b): void
    {
        $result = $this->comparator->compare($a, $b);

        $this->assertSame(0, $result);
    }

    /** @return array<string, array{string, string}> */
    public static function provideEqualCases(): array
    {
        return [
            'same word' => ['test', 'test'],
            'both empty' => ['', ''],
            'same case' => ['Apple', 'Apple'],
            'same numbers' => ['123', '123'],
            'same special chars' => ['!@#', '!@#'],
        ];
    }

    #[Test]
    #[DataProvider('provideGreaterThanCases')]
    #[TestDox('Returns positive when first string is greater than second (case-sensitive)')]
    public function compare_ReturnsPositive_WhenFirstIsGreater(string $a, string $b): void
    {
        $result = $this->comparator->compare($a, $b);

        $this->assertGreaterThan(0, $result);
    }

    /** @return array<string, array{string, string}> */
    public static function provideGreaterThanCases(): array
    {
        return [
            'reverse alphabetical' => ['zebra', 'apple'],
            'longer before shorter' => ['apple', 'app'],
            'lowercase after uppercase' => ['apple', 'Apple'],
            'all caps reverse' => ['BANANA', 'APPLE'],
            'with numbers reverse' => ['a2', 'a1'],
            'text after empty' => ['text', ''],
        ];
    }

    #[Test]
    #[DataProvider('provideCaseInsensitiveEqualCases')]
    #[TestDox('Returns zero when strings are equal ignoring case')]
    public function compare_CaseInsensitive_ReturnsZero_WhenStringsDifferOnlyByCase(string $a, string $b): void
    {
        $result = $this->caseInsensitiveComparator->compare($a, $b);

        $this->assertSame(0, $result);
    }

    /** @return array<string, array{string, string}> */
    public static function provideCaseInsensitiveEqualCases(): array
    {
        return [
            'lowercase vs uppercase' => ['apple', 'APPLE'],
            'mixed case' => ['Apple', 'aPPLe'],
            'first letter case' => ['apple', 'Apple'],
            'all variations' => ['TeSt', 'tEsT'],
        ];
    }

    #[Test]
    #[DataProvider('provideCaseInsensitiveLessThanCases')]
    #[TestDox('Returns negative respecting order ignoring case')]
    public function compare_CaseInsensitive_ReturnsNegative_WhenFirstIsLess(string $a, string $b): void
    {
        $result = $this->caseInsensitiveComparator->compare($a, $b);

        $this->assertLessThan(0, $result);
    }

    /** @return array<string, array{string, string}> */
    public static function provideCaseInsensitiveLessThanCases(): array
    {
        return [
            'alphabetical order' => ['apple', 'banana'],
            'case mixed order' => ['Apple', 'banana'],
            'reverse case' => ['APPLE', 'banana'],
        ];
    }

    #[Test]
    #[DataProvider('provideInvalidTypes')]
    #[TestDox('Throws InvalidArgumentException when given non-string')]
    public function compare_ThrowsInvalidArgumentException_WhenGivenNonString(mixed $a, mixed $b): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Compare only strings');

        $this->comparator->compare($a, $b);
    }

    /** @return array<string, array{mixed, mixed}> */
    public static function provideInvalidTypes(): array
    {
        return [
            'int vs string' => [123, 'abc'],
            'string vs int' => ['abc', 123],
            'float vs string' => [1.5, 'abc'],
            'null vs string' => [null, 'abc'],
            'array vs string' => [[], 'abc'],
            'object vs string' => [new \stdClass(), 'abc'],
            'bool vs string' => [true, 'abc'],
        ];
    }
}
