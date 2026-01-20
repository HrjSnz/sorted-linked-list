<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit;

use HrjSnz\SortedLinkedList\BetweenBoundary;
use HrjSnz\SortedLinkedList\Comparator\IntComparator;
use HrjSnz\SortedLinkedList\SortedLinkedListEngine;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(SortedLinkedListEngine::class)]
final class SortedLinkedListEngineTest extends TestCase
{
    /** @var TestableSortedLinkedListEngine */
    private TestableSortedLinkedListEngine $engine;

    protected function setUp(): void
    {
        $this->engine = new TestableSortedLinkedListEngine(new IntComparator());
    }

    #[Test]
    #[TestDox('addAll with empty array does nothing')]
    public function addAll_WithEmptyArray_DoesNothing(): void
    {
        $this->engine->testAddAll([]);

        $this->assertCount(0, $this->engine);
    }

    #[Test]
    #[TestDox('addAll with empty iterator does nothing')]
    public function addAll_WithEmptyIterator_DoesNothing(): void
    {
        $this->engine->testAddAll(new \EmptyIterator());

        $this->assertCount(0, $this->engine);
    }

    #[Test]
    #[TestDox('addAll with generator adds all values')]
    public function addAll_WithGenerator_AddsAllValues(): void
    {
        $generator = (function () {
            yield 3;
            yield 1;
            yield 2;
        })();

        $this->engine->testAddAll($generator);

        $this->assertSame([1, 2, 3], iterator_to_array($this->engine));
    }

    #[Test]
    #[DataProvider('provideIterableTypes')]
    #[TestDox('addAll works with different iterable types')]
    public function addAll_WorksWith_DifferentIterableTypes(iterable $values, array $expected): void
    {
        $this->engine->testAddAll($values);

        $this->assertSame($expected, iterator_to_array($this->engine));
    }

    /** @return array<string, array{iterable, array<int>}> */
    public static function provideIterableTypes(): array
    {
        return [
            'array' => [[3, 1, 2], [1, 2, 3]],
            'ArrayIterator' => [new \ArrayIterator([3, 1, 2]), [1, 2, 3]],
            'generator' => [(function () {
                yield 3;
                yield 1;
                yield 2;
            })(), [1, 2, 3]],
        ];
    }

    #[Test]
    #[TestDox('insert adds value in sorted order')]
    public function insert_AddsValue_InSortedOrder(): void
    {
        $this->engine->testInsert(5);
        $this->engine->testInsert(3);
        $this->engine->testInsert(7);
        $this->engine->testInsert(1);

        $this->assertSame([1, 3, 5, 7], iterator_to_array($this->engine));
    }

    #[Test]
    #[TestDox('insert with duplicates allowed adds all values')]
    public function insert_WithDuplicatesAllowed_AddsAllValues(): void
    {
        $this->engine->testInsert(5);
        $this->engine->testInsert(5);
        $this->engine->testInsert(5);

        $this->assertSame([5, 5, 5], iterator_to_array($this->engine));
    }

    #[Test]
    #[TestDox('insert with duplicates not allowed skips duplicate')]
    public function insert_WithDuplicatesNotAllowed_SkipsDuplicate(): void
    {
        $engine = new TestableSortedLinkedListEngine(new IntComparator(), allowDuplicates: false);

        $engine->testInsert(5);
        $engine->testInsert(5);
        $engine->testInsert(5);

        $this->assertSame([5], iterator_to_array($engine));
    }

    #[Test]
    #[TestDox('insert at head when list is empty')]
    public function insert_AtHead_WhenListIsEmpty(): void
    {
        $this->engine->testInsert(42);

        $this->assertSame(42, $this->engine->first());
        $this->assertSame(42, $this->engine->last());
        $this->assertCount(1, $this->engine);
    }

    #[Test]
    #[TestDox('insert at head when value is smallest')]
    public function insert_AtHead_WhenValueIsSmallest(): void
    {
        $this->engine->testInsert(10);
        $this->engine->testInsert(5);

        $this->assertSame(5, $this->engine->first());
    }

    #[Test]
    #[TestDox('insert at tail when value is largest')]
    public function insert_AtTail_WhenValueIsLargest(): void
    {
        $this->engine->testInsert(5);
        $this->engine->testInsert(10);

        $this->assertSame(10, $this->engine->last());
    }

    #[Test]
    #[TestDox('delete returns false when value not found')]
    public function delete_ReturnsFalse_WhenValueNotFound(): void
    {
        $this->engine->testInsert(5);
        $this->engine->testInsert(10);

        $result = $this->engine->testDelete(99);

        $this->assertFalse($result);
        $this->assertCount(2, $this->engine);
    }

    #[Test]
    #[TestDox('delete returns true and removes value when found')]
    public function delete_ReturnsTrue_RemovesValueWhenFound(): void
    {
        $this->engine->testInsert(5);
        $this->engine->testInsert(10);
        $this->engine->testInsert(15);

        $result = $this->engine->testDelete(10);

        $this->assertTrue($result);
        $this->assertSame([5, 15], iterator_to_array($this->engine));
    }

    #[Test]
    #[TestDox('delete removes head element')]
    public function delete_RemovesHeadElement(): void
    {
        $this->engine->testInsert(5);
        $this->engine->testInsert(10);

        $result = $this->engine->testDelete(5);

        $this->assertTrue($result);
        $this->assertSame(10, $this->engine->first());
    }

    #[Test]
    #[TestDox('delete removes tail element')]
    public function delete_RemovesTailElement(): void
    {
        $this->engine->testInsert(5);
        $this->engine->testInsert(10);

        $result = $this->engine->testDelete(10);

        $this->assertTrue($result);
        $this->assertSame(5, $this->engine->last());
    }

    #[Test]
    #[TestDox('delete from empty list returns false')]
    public function delete_FromEmptyList_ReturnsFalse(): void
    {
        $result = $this->engine->testDelete(5);

        $this->assertFalse($result);
    }

    #[Test]
    #[TestDox('deleteAll removes all occurrences of value')]
    public function deleteAll_RemovesAllOccurrences_OfValue(): void
    {
        $this->engine->testInsert(5);
        $this->engine->testInsert(5);
        $this->engine->testInsert(5);
        $this->engine->testInsert(10);
        $this->engine->testInsert(5);

        $count = $this->engine->testDeleteAll(5);

        $this->assertSame(4, $count);
        $this->assertSame([10], iterator_to_array($this->engine));
    }

    #[Test]
    #[TestDox('deleteAll returns zero when value not found')]
    public function deleteAll_ReturnsZero_WhenValueNotFound(): void
    {
        $this->engine->testInsert(10);
        $this->engine->testInsert(20);

        $count = $this->engine->testDeleteAll(99);

        $this->assertSame(0, $count);
        $this->assertCount(2, $this->engine);
    }

    #[Test]
    #[TestDox('deleteAll from empty list returns zero')]
    public function deleteAll_FromEmptyList_ReturnsZero(): void
    {
        $count = $this->engine->testDeleteAll(5);

        $this->assertSame(0, $count);
    }

    #[Test]
    #[TestDox('greaterThan with inclusive boundary includes equal values')]
    public function greaterThan_WithInclusiveBoundary_IncludesEqualValues(): void
    {
        $this->engine->testAddAll([1, 3, 5, 7, 9]);

        $result = iterator_to_array($this->engine->testGreaterThan(5));

        $this->assertSame([5, 7, 9], $result);
    }

    #[Test]
    #[TestDox('greaterThan with exclusive boundary excludes equal values')]
    public function greaterThan_WithExclusiveBoundary_ExcludesEqualValues(): void
    {
        $this->engine->testAddAll([1, 3, 5, 7, 9]);

        $result = iterator_to_array($this->engine->testGreaterThan(5, BetweenBoundary::EXCLUSIVE));

        $this->assertSame([7, 9], $result);
    }

    #[Test]
    #[TestDox('greaterThan returns empty generator when no values match')]
    public function greaterThan_ReturnsEmpty_WhenNoValuesMatch(): void
    {
        $this->engine->testAddAll([1, 2, 3]);

        $result = iterator_to_array($this->engine->testGreaterThan(10));

        $this->assertSame([], $result);
    }

    #[Test]
    #[TestDox('greaterThan returns all values when min is smallest')]
    public function greaterThan_ReturnsAllValues_WhenMinIsSmallest(): void
    {
        $this->engine->testAddAll([1, 2, 3, 4, 5]);

        $result = iterator_to_array($this->engine->testGreaterThan(0));

        $this->assertSame([1, 2, 3, 4, 5], $result);
    }

    #[Test]
    #[TestDox('lessThan with inclusive boundary includes equal values')]
    public function lessThan_WithInclusiveBoundary_IncludesEqualValues(): void
    {
        $this->engine->testAddAll([1, 3, 5, 7, 9]);

        $result = iterator_to_array($this->engine->testLessThan(5));

        $this->assertSame([1, 3, 5], $result);
    }

    #[Test]
    #[TestDox('lessThan with exclusive boundary excludes equal values')]
    public function lessThan_WithExclusiveBoundary_ExcludesEqualValues(): void
    {
        $this->engine->testAddAll([1, 3, 5, 7, 9]);

        $result = iterator_to_array($this->engine->testLessThan(5, BetweenBoundary::EXCLUSIVE));

        $this->assertSame([1, 3], $result);
    }

    #[Test]
    #[TestDox('lessThan returns empty generator when no values match')]
    public function lessThan_ReturnsEmpty_WhenNoValuesMatch(): void
    {
        $this->engine->testAddAll([10, 20, 30]);

        $result = iterator_to_array($this->engine->testLessThan(5));

        $this->assertSame([], $result);
    }

    #[Test]
    #[TestDox('lessThan stops iteration early when value exceeds max')]
    public function lessThan_StopsIterationEarly_WhenValueExceedsMax(): void
    {
        $this->engine->testAddAll([1, 3, 5, 7, 9]);

        $result = iterator_to_array($this->engine->testLessThan(5));

        $this->assertSame([1, 3, 5], $result);
        $this->assertCount(5, $this->engine);
    }

    #[Test]
    #[DataProvider('provideInRangeCases')]
    #[TestDox('inRange returns values within boundaries')]
    public function inRange_ReturnsValues_WithinBoundaries(
        array $values,
        int $min,
        int $max,
        BetweenBoundary $minBoundary,
        BetweenBoundary $maxBoundary,
        array $expected,
    ): void {
        $this->engine->testAddAll($values);

        $result = iterator_to_array($this->engine->testInRange($min, $max, $minBoundary, $maxBoundary));

        $this->assertSame($expected, $result);
    }

    /** @return array<string, array{array<int>, int, int, BetweenBoundary, BetweenBoundary, array<int>}> */
    public static function provideInRangeCases(): array
    {
        return [
            'both_inclusive' => [[1, 2, 3, 4, 5], 2, 4, BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, [2, 3, 4]],
            'both_exclusive' => [[1, 2, 3, 4, 5], 2, 4, BetweenBoundary::EXCLUSIVE, BetweenBoundary::EXCLUSIVE, [3]],
            'min_exclusive_max_inclusive' => [[1, 2, 3, 4, 5], 2, 4, BetweenBoundary::EXCLUSIVE, BetweenBoundary::INCLUSIVE, [3, 4]],
            'min_inclusive_max_exclusive' => [[1, 2, 3, 4, 5], 2, 4, BetweenBoundary::INCLUSIVE, BetweenBoundary::EXCLUSIVE, [2, 3]],
            'no_match' => [[1, 2, 3], 10, 20, BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, []],
            'single_value_in_range' => [[5], 5, 5, BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, [5]],
        ];
    }

    #[Test]
    #[TestDox('inRange throws exception when min is greater than max')]
    public function inRange_ThrowsException_WhenMinGreaterThanMax(): void
    {
        $this->engine->testAddAll([1, 2, 3]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid range: lower bound must come before upper bound');

        iterator_to_array($this->engine->testInRange(10, 5));
    }

    #[Test]
    #[TestDox('has returns true when value exists')]
    public function has_ReturnsTrue_WhenValueExists(): void
    {
        $this->engine->testInsert(5);
        $this->engine->testInsert(10);

        $result = $this->engine->testHas(10);

        $this->assertTrue($result);
    }

    #[Test]
    #[TestDox('has returns false when value does not exist')]
    public function has_ReturnsFalse_WhenValueDoesNotExist(): void
    {
        $this->engine->testInsert(5);
        $this->engine->testInsert(10);

        $result = $this->engine->testHas(99);

        $this->assertFalse($result);
    }

    #[Test]
    #[TestDox('has returns false for empty list')]
    public function has_ReturnsFalse_ForEmptyList(): void
    {
        $result = $this->engine->testHas(5);

        $this->assertFalse($result);
    }

    #[Test]
    #[TestDox('has finds value at head')]
    public function has_FindsValue_AtHead(): void
    {
        $this->engine->testInsert(5);
        $this->engine->testInsert(10);

        $this->assertTrue($this->engine->testHas(5));
    }

    #[Test]
    #[TestDox('has finds value at tail')]
    public function has_FindsValue_AtTail(): void
    {
        $this->engine->testInsert(5);
        $this->engine->testInsert(10);

        $this->assertTrue($this->engine->testHas(10));
    }
}
