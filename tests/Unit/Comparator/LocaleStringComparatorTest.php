<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\Comparator;

use HrjSnz\SortedLinkedList\Comparator\LocaleStringComparator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(LocaleStringComparator::class)]
final class LocaleStringComparatorTest extends TestCase
{
    #[Test]
    #[DataProvider('provideLessThanCases')]
    #[TestDox('Returns negative when first string is less than second')]
    public function compare_ReturnsNegative_WhenFirstIsLess(LocaleStringComparator $comparator, string $a, string $b): void
    {
        $result = $comparator->compare($a, $b);

        $this->assertLessThan(0, $result);
    }

    /** @return array<string, array{LocaleStringComparator, string, string}> */
    public static function provideLessThanCases(): array
    {
        $enUs = new LocaleStringComparator('en_US');

        return [
            'en_US alphabetical' => [$enUs, 'apple', 'banana'],
            'en_US empty first' => [$enUs, '', 'text'],
            'en_US single char' => [$enUs, 'a', 'z'],
        ];
    }

    #[Test]
    #[DataProvider('provideEqualCases')]
    #[TestDox('Returns zero when strings are equal')]
    public function compare_ReturnsZero_WhenStringsAreEqual(LocaleStringComparator $comparator, string $a, string $b): void
    {
        $result = $comparator->compare($a, $b);

        $this->assertSame(0, $result);
    }

    /** @return array<string, array{LocaleStringComparator, string, string}> */
    public static function provideEqualCases(): array
    {
        $enUs = new LocaleStringComparator('en_US');

        return [
            'en_US same word' => [$enUs, 'test', 'test'],
            'en_US both empty' => [$enUs, '', ''],
        ];
    }

    #[Test]
    #[DataProvider('provideGreaterThanCases')]
    #[TestDox('Returns positive when first string is greater than second')]
    public function compare_ReturnsPositive_WhenFirstIsGreater(LocaleStringComparator $comparator, string $a, string $b): void
    {
        $result = $comparator->compare($a, $b);

        $this->assertGreaterThan(0, $result);
    }

    /** @return array<string, array{LocaleStringComparator, string, string}> */
    public static function provideGreaterThanCases(): array
    {
        $enUs = new LocaleStringComparator('en_US');

        return [
            'en_US reverse alphabetical' => [$enUs, 'zebra', 'apple'],
            'en_US text after empty' => [$enUs, 'text', ''],
        ];
    }

    #[Test]
    #[TestDox('Czech locale: ch comes after h')]
    public function compare_CzechLocale_ChComesAfterH(): void
    {
        $comparator = new LocaleStringComparator('cs_CZ');

        $result = $comparator->compare('h', 'ch');

        $this->assertLessThan(0, $result);
    }

    #[Test]
    #[TestDox('German locale: treats umlauts correctly')]
    public function compare_GermanLocale_TreatsUmlautsCorrectly(): void
    {
        $comparator = new LocaleStringComparator('de_DE');

        $result1 = $comparator->compare('ä', 'b');
        $this->assertLessThan(0, $result1);
    }

    #[Test]
    #[DataProvider('provideStrengthLevels')]
    #[TestDox('Respects different Collator strength levels')]
    public function compare_RespectsStrengthLevel(int $strength, string $a, string $b, int $expectedComparison): void
    {
        $comparator = new LocaleStringComparator('en_US', $strength);

        $result = $comparator->compare($a, $b);

        if ($expectedComparison < 0) {
            $this->assertLessThan(0, $result);
        } elseif ($expectedComparison > 0) {
            $this->assertGreaterThan(0, $result);
        } else {
            $this->assertSame(0, $result);
        }
    }

    /** @return array<string, array{int, string, string, int}> */
    public static function provideStrengthLevels(): array
    {
        return [
            'PRIMARY ignores case' => [\Collator::PRIMARY, 'apple', 'APPLE', 0],
            'SECONDARY respects accents' => [\Collator::SECONDARY, 'cafe', 'café', -1],
            'TERTIARY distinguishes case' => [\Collator::TERTIARY, 'apple', 'APPLE', -1],
        ];
    }

    #[Test]
    #[DataProvider('provideInvalidTypes')]
    #[TestDox('Throws InvalidArgumentException when given non-string')]
    public function compare_ThrowsInvalidArgumentException_WhenGivenNonString(mixed $a, mixed $b): void
    {
        $comparator = new LocaleStringComparator('en_US');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Compare only strings');

        $comparator->compare($a, $b);
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
        ];
    }
}
