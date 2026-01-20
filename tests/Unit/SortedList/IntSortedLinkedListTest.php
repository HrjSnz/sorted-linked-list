<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\SortedList;

use HrjSnz\SortedLinkedList\BetweenBoundary;
use HrjSnz\SortedLinkedList\Comparator\IntComparator;
use HrjSnz\SortedLinkedList\Comparator\ReverseComparator;
use HrjSnz\SortedLinkedList\SortedList\IntSortedLinkedList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(IntSortedLinkedList::class)]
final class IntSortedLinkedListTest extends TestCase
{
    private IntComparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new IntComparator();
    }

    private function createList(bool $allowDuplicates = true): IntSortedLinkedList
    {
        return new IntSortedLinkedList($this->comparator, $allowDuplicates);
    }

    #[Test]
    #[TestDox('List is empty when created')]
    public function constructor_IsEmpty_WhenCreated(): void
    {
        $list = $this->createList();

        $this->assertTrue($list->isEmpty());
        $this->assertCount(0, $list);
    }

    #[Test]
    #[TestDox('First and last are null when list is empty')]
    public function firstAndLast_returnNull_WhenListIsEmpty(): void
    {
        $list = $this->createList();

        $this->assertNull($list->first());
        $this->assertNull($list->last());
    }

    /**
     * @param array<int> $initialValues
     * @param array<int> $expectedValues
     */
    #[Test]
    #[DataProvider('provideSingleAddCases')]
    #[TestDox('Add single value to list')]
    public function add_InsertsValue_InSortedOrder(
        array $initialValues,
        int $valueToAdd,
        array $expectedValues
    ): void {
        $list = $this->createList();

        foreach ($initialValues as $value) {
            $list->add($value);
        }

        $list->add($valueToAdd);

        $this->assertSame($expectedValues, iterator_to_array($list));
    }

    /** @return array<string, array{array<int>, int, array<int>}> */
    public static function provideSingleAddCases(): array
    {
        return [
            'add_to_empty' => [[], 5, [5]],
            'add_to_head_smaller' => [[10, 20, 30], 5, [5, 10, 20, 30]],
            'add_to_head_larger_reverse' => [[30, 20, 10], 5, [5, 10, 20, 30]],
            'add_to_tail_larger' => [[10, 20, 30], 35, [10, 20, 30, 35]],
            'add_to_tail_smaller_reverse' => [[30, 20, 10], 5, [5, 10, 20, 30]],
            'add_in_middle' => [[10, 30], 20, [10, 20, 30]],
            'add_negative_to_empty' => [[], -5, [-5]],
            'add_negative_to_positive' => [[1, 2, 3], -1, [-1, 1, 2, 3]],
            'add_zero_to_empty' => [[], 0, [0]],
            'add_zero_to_positive' => [[1, 2, 3], 0, [0, 1, 2, 3]],
            'add_zero_to_negative' => [[-3, -2, -1], 0, [-3, -2, -1, 0]],
            'add_int_min_to_empty' => [[], PHP_INT_MIN, [PHP_INT_MIN]],
            'add_int_min_to_positive' => [[0, 100], PHP_INT_MIN, [PHP_INT_MIN, 0, 100]],
            'add_int_max_to_empty' => [[], PHP_INT_MAX, [PHP_INT_MAX]],
            'add_int_max_to_negative' => [[-100, 0], PHP_INT_MAX, [-100, 0, PHP_INT_MAX]],
        ];
    }

    #[Test]
    #[TestDox('Allows duplicates when allowDuplicates is true')]
    public function add_AllowsDuplicates_WhenModeEnabled(): void
    {
        $list = $this->createList(allowDuplicates: true);

        $list->add(5);
        $list->add(10);
        $list->add(5);
        $list->add(5);

        $this->assertSame([5, 5, 5, 10], iterator_to_array($list));
        $this->assertCount(4, $list);
    }

    #[Test]
    #[TestDox('Rejects duplicates when allowDuplicates is false')]
    public function add_RejectsDuplicates_WhenModeDisabled(): void
    {
        $list = $this->createList(allowDuplicates: false);

        $list->add(5);
        $list->add(10);
        $list->add(5);
        $list->add(5);

        $this->assertSame([5, 10], iterator_to_array($list));
        $this->assertCount(2, $list);
    }

    /**
     * @param array<int> $initialValues
     * @param array<int> $valuesToAdd
     * @param array<int> $expectedValues
     */
    #[Test]
    #[DataProvider('provideAddMultipleCases')]
    #[TestDox('Add multiple values maintains sorted order')]
    public function addMultiple_MaintainsSortedOrder(
        array $initialValues,
        array $valuesToAdd,
        array $expectedValues
    ): void {
        $list = $this->createList();

        foreach ($initialValues as $value) {
            $list->add($value);
        }

        $list->addMultiple(...$valuesToAdd);

        $this->assertSame($expectedValues, iterator_to_array($list));
    }

    /** @return array<string, array{array<int>, array<int>, array<int>}> */
    public static function provideAddMultipleCases(): array
    {
        return [
            'add_to_empty' => [[], [3, 1, 2], [1, 2, 3]],
            'add_already_sorted' => [[1, 2], [3, 4, 5], [1, 2, 3, 4, 5]],
            'add_reverse_sorted' => [[], [5, 4, 3, 2, 1], [1, 2, 3, 4, 5]],
            'add_random_order' => [[10], [5, 15, 3, 20], [3, 5, 10, 15, 20]],
            'add_with_duplicates_enabled' => [[], [5, 2, 5, 2], [2, 2, 5, 5]],
            'add_mixed_signs' => [[], [-5, 5, -10, 10], [-10, -5, 5, 10]],
            'add_empty_array' => [[1, 2, 3], [], [1, 2, 3]],
            'add_single_value' => [[], [42], [42]],
        ];
    }

    #[Test]
    #[TestDox('addMultiple rejects duplicates when mode disabled')]
    public function addMultiple_RejectsDuplicates_WhenModeDisabled(): void
    {
        $list = $this->createList(allowDuplicates: false);

        $list->addMultiple(5, 2, 5, 3, 2, 5);

        $this->assertSame([2, 3, 5], iterator_to_array($list));
    }

    /** @param array<int> $values */
    #[Test]
    #[DataProvider('provideContainsCases')]
    #[TestDox('Returns correct boolean for contains check')]
    public function contains_ReturnsCorrectBoolean(
        array $values,
        int $searchValue,
        bool $expected
    ): void {
        $list = $this->createList();
        $list->addMultiple(...$values);

        $result = $list->contains($searchValue);

        $this->assertSame($expected, $result);
    }

    /** @return array<string, array{array<int>, int, bool}> */
    public static function provideContainsCases(): array
    {
        return [
            'exists_in_single_element' => [[5], 5, true],
            'exists_in_middle' => [[1, 5, 10], 5, true],
            'exists_at_head' => [[1, 2, 3], 1, true],
            'exists_at_tail' => [[1, 2, 3], 3, true],
            'not_exists_smaller' => [[5, 10], 1, false],
            'not_exists_larger' => [[5, 10], 15, false],
            'not_exists_between' => [[1, 5], 3, false],
            'empty_list' => [[], 5, false],
            'negative_exists' => [[-5, 0, 5], -5, true],
            'zero_exists' => [[-5, 0, 5], 0, true],
            'int_min_exists' => [[PHP_INT_MIN, 0], PHP_INT_MIN, true],
            'int_max_exists' => [[0, PHP_INT_MAX], PHP_INT_MAX, true],
            'not_exists_int_min' => [[-100, 0], PHP_INT_MIN, false],
            'not_exists_int_max' => [[0, 100], PHP_INT_MAX, false],
        ];
    }

    /**
     * @param array<int> $values
     * @param array<int> $expectedValues
     */
    #[Test]
    #[DataProvider('provideRemoveCases')]
    #[TestDox('Removes value and returns success status')]
    public function remove_RemovesValue_ReturnsSuccess(
        array $values,
        int $valueToRemove,
        bool $expectedResult,
        array $expectedValues
    ): void {
        $list = $this->createList();
        $list->addMultiple(...$values);

        $result = $list->remove($valueToRemove);

        $this->assertSame($expectedResult, $result);
        $this->assertSame($expectedValues, iterator_to_array($list));
    }

    /** @return array<string, array{array<int>, int, bool, array<int>}> */
    public static function provideRemoveCases(): array
    {
        return [
            'remove_from_single_element' => [[5], 5, true, []],
            'remove_from_head' => [[1, 2, 3], 1, true, [2, 3]],
            'remove_from_tail' => [[1, 2, 3], 3, true, [1, 2]],
            'remove_from_middle' => [[1, 2, 3], 2, true, [1, 3]],
            'remove_not_exists' => [[1, 2, 3], 5, false, [1, 2, 3]],
            'remove_from_empty' => [[], 5, false, []],
            'remove_negative' => [[-5, 0, 5], -5, true, [0, 5]],
            'remove_zero' => [[-5, 0, 5], 0, true, [-5, 5]],
            'remove_int_min' => [[PHP_INT_MIN, 0], PHP_INT_MIN, true, [0]],
            'remove_int_max' => [[0, PHP_INT_MAX], PHP_INT_MAX, true, [0]],
        ];
    }

    #[Test]
    #[TestDox('Remove only removes first occurrence when duplicates enabled')]
    public function remove_RemovesOnlyFirstOccurrence_WhenDuplicatesEnabled(): void
    {
        $list = $this->createList(allowDuplicates: true);
        $list->addMultiple(5, 10, 5, 5, 10);

        $result = $list->remove(5);

        $this->assertTrue($result);
        $this->assertSame([5, 5, 10, 10], iterator_to_array($list));
        $this->assertCount(4, $list);
    }

    #[Test]
    #[TestDox('Remove returns false when duplicates disabled and value not found')]
    public function remove_ReturnsFalse_WhenDuplicatesDisabledAndNotFound(): void
    {
        $list = $this->createList(allowDuplicates: false);
        $list->addMultiple(1, 2, 3);

        $result = $list->remove(5);

        $this->assertFalse($result);
    }

    /**
     * @param array<int> $values
     * @param array<int> $expectedValues
     */
    #[Test]
    #[DataProvider('provideRemoveAllCases')]
    #[TestDox('Removes all occurrences and returns count')]
    public function removeAll_RemovesAllOccurrences_ReturnsCount(
        bool $allowDuplicates,
        array $values,
        int $valueToRemove,
        int $expectedCount,
        array $expectedValues
    ): void {
        $list = $this->createList(allowDuplicates: $allowDuplicates);
        $list->addMultiple(...$values);

        $count = $list->removeAll($valueToRemove);

        $this->assertSame($expectedCount, $count);
        $this->assertSame($expectedValues, iterator_to_array($list));
    }

    /** @return array<string, array{bool, array<int>, int, int, array<int>}> */
    public static function provideRemoveAllCases(): array
    {
        return [
            'remove_all_duplicates_enabled' => [true, [5, 10, 5, 5, 10], 5, 3, [10, 10]],
            'remove_all_duplicates_disabled' => [false, [5, 10, 5, 5, 10], 5, 1, [10]],
            'remove_single_occurrence' => [true, [1, 2, 3], 2, 1, [1, 3]],
            'remove_not_exists' => [true, [1, 2, 3], 5, 0, [1, 2, 3]],
            'remove_from_empty' => [true, [], 5, 0, []],
            'remove_all_elements' => [true, [5, 5, 5], 5, 3, []],
            'remove_negative' => [true, [-5, 0, 5, -5], -5, 2, [0, 5]],
            'remove_zero' => [true, [0, 1, 0, 2], 0, 2, [1, 2]],
        ];
    }

    /**
     * @param array<int> $values
     * @param array<int> $expected
     */
    #[Test]
    #[DataProvider('provideFindInRangeCases')]
    #[TestDox('Finds values within range boundaries')]
    public function findInRange_ReturnsValues_WithinBoundaries(
        array $values,
        int $min,
        int $max,
        BetweenBoundary $minBoundary,
        BetweenBoundary $maxBoundary,
        array $expected
    ): void {
        $list = $this->createList();
        $list->addMultiple(...$values);

        $result = $list->findInRange($min, $max, $minBoundary, $maxBoundary);

        $this->assertSame($expected, iterator_to_array($result));
    }

    /** @return array<string, array{array<int>, int, int, BetweenBoundary, BetweenBoundary, array<int>}> */
    public static function provideFindInRangeCases(): array
    {
        return [
            'both_inclusive' => [[1, 2, 3, 4, 5], 2, 4, BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, [2, 3, 4]],
            'both_exclusive' => [[1, 2, 3, 4, 5], 2, 4, BetweenBoundary::EXCLUSIVE, BetweenBoundary::EXCLUSIVE, [3]],
            'min_exclusive_max_inclusive' => [[1, 2, 3, 4, 5], 2, 4, BetweenBoundary::EXCLUSIVE, BetweenBoundary::INCLUSIVE, [3, 4]],
            'min_inclusive_max_exclusive' => [[1, 2, 3, 4, 5], 2, 4, BetweenBoundary::INCLUSIVE, BetweenBoundary::EXCLUSIVE, [2, 3]],
            'no_match' => [[1, 2, 3], 10, 20, BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, []],
            'negative_range' => [[-5, -3, -1, 0, 1], -4, 0, BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, [-3, -1, 0]],
            'include_negative_and_positive' => [[-5, 0, 5], -5, 5, BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, [-5, 0, 5]],
            'zero_boundary' => [[-2, -1, 0, 1, 2], 0, 0, BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, [0]],
            'duplicates_in_range' => [[1, 2, 2, 3, 3, 3], 2, 3, BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, [2, 2, 3, 3, 3]],
        ];
    }

    /** @param array<int> $values */
    #[Test]
    #[DataProvider('provideCountCases')]
    #[TestDox('Count returns correct number of elements')]
    public function count_ReturnsCorrectNumber(array $values, int $expectedCount): void
    {
        $list = $this->createList();
        $list->addMultiple(...$values);

        $this->assertCount($expectedCount, $list);
        $this->assertSame($expectedCount, $list->count());
    }

    /** @return array<string, array{array<int>, int}> */
    public static function provideCountCases(): array
    {
        return [
            'empty_list' => [[], 0],
            'single_element' => [[5], 1],
            'multiple_elements' => [[1, 2, 3, 4, 5], 5],
            'with_duplicates' => [[1, 1, 2, 2, 3], 5],
        ];
    }

    /** @param array<int> $values */
    #[Test]
    #[DataProvider('provideIsEmptyCases')]
    #[TestDox('IsEmpty returns correct boolean')]
    public function isEmpty_ReturnsCorrectBoolean(array $values, bool $expected): void
    {
        $list = $this->createList();
        $list->addMultiple(...$values);

        $this->assertSame($expected, $list->isEmpty());
    }

    /** @return array<string, array{array<int>, bool}> */
    public static function provideIsEmptyCases(): array
    {
        return [
            'empty_list' => [[], true],
            'single_element' => [[5], false],
            'multiple_elements' => [[1, 2, 3], false],
        ];
    }

    /** @param array<int> $values */
    #[Test]
    #[DataProvider('provideFirstLastCases')]
    #[TestDox('First and last return correct values')]
    public function firstAndLast_ReturnCorrectValues(
        array $values,
        ?int $expectedFirst,
        ?int $expectedLast
    ): void {
        $list = $this->createList();
        $list->addMultiple(...$values);

        $this->assertSame($expectedFirst, $list->first());
        $this->assertSame($expectedLast, $list->last());
    }

    /** @return array<string, array{array<int>, ?int, ?int}> */
    public static function provideFirstLastCases(): array
    {
        return [
            'empty_list' => [[], null, null],
            'single_element' => [[5], 5, 5],
            'multiple_elements' => [[1, 2, 3, 4, 5], 1, 5],
            'negative_first' => [[-5, 0, 5], -5, 5],
            'reverse_insertion' => [[5, 4, 3, 2, 1], 1, 5],
            'with_duplicates' => [[1, 1, 2, 2], 1, 2],
        ];
    }

    #[Test]
    #[TestDox('Iterate returns all values in sorted order')]
    public function getIterator_ReturnsAllValues_InSortedOrder(): void
    {
        $list = $this->createList();
        $list->addMultiple(5, 2, 8, 1, 9);

        $values = iterator_to_array($list);

        $this->assertSame([1, 2, 5, 8, 9], $values);
    }

    /**
     * @param array<int> $values
     * @param array<int> $expectedDuplicates
     */
    #[Test]
    #[DataProvider('provideGetDuplicatesCases')]
    #[TestDox('GetDuplicates returns array of duplicated values')]
    public function getDuplicates_ReturnsArray_WithDuplicateValues(
        bool $allowDuplicates,
        array $values,
        array $expectedDuplicates
    ): void {
        $list = $this->createList(allowDuplicates: $allowDuplicates);
        $list->addMultiple(...$values);

        $duplicates = $list->getDuplicates();

        $this->assertSame($expectedDuplicates, $duplicates);
    }

    /** @return array<string, array{bool, array<int>, array<int>}> */
    public static function provideGetDuplicatesCases(): array
    {
        return [
            'no_duplicates' => [true, [1, 2, 3, 4, 5], []],
            'single_duplicate' => [true, [1, 2, 2, 3], [2]],
            'multiple_duplicates' => [true, [1, 1, 2, 2, 3, 3], [1, 2, 3]],
            'triple_occurrence' => [true, [1, 2, 2, 2, 3], [2]],
            'all_duplicates' => [true, [5, 5, 5, 5], [5]],
            'empty_list' => [true, [], []],
            'single_element' => [true, [5], []],
            'duplicates_disabled_empty' => [false, [1, 2, 2, 3], []],
        ];
    }

    /**
     * @param array<int> $values
     * @param array<int> $expected
     */
    #[Test]
    #[DataProvider('provideGetDuplicatesWithCountCases')]
    #[TestDox('GetDuplicatesWithCount returns array with value and count')]
    public function getDuplicatesWithCount_ReturnsArray_WithValueAndCount(
        bool $allowDuplicates,
        array $values,
        array $expected
    ): void {
        $list = $this->createList(allowDuplicates: $allowDuplicates);
        $list->addMultiple(...$values);

        $result = $list->getDuplicatesWithCount();

        $this->assertSame($expected, $result);
    }

    /** @return array<string, array{bool, array<int>, array<int, array{value: int, count: int}>}> */
    public static function provideGetDuplicatesWithCountCases(): array
    {
        return [
            'no_duplicates' => [true, [1, 2, 3], []],
            'with_duplicates' => [true, [1, 2, 2, 3], [['value' => 2, 'count' => 2]]],
            'multiple_duplicates' => [true, [1, 1, 2, 2, 2, 3], [['value' => 1, 'count' => 2], ['value' => 2, 'count' => 3]]],
            'empty_list' => [true, [], []],
            'single_element' => [true, [5], []],
        ];
    }

    #[Test]
    #[TestDox('GetComparator returns the set comparator')]
    public function getComparator_ReturnsSetComparator(): void
    {
        $list = $this->createList();
        $comparator = $list->getComparator();

        $this->assertSame($this->comparator, $comparator);
    }

    #[Test]
    #[TestDox('SetComparator changes the comparator')]
    public function setComparator_ChangesComparator(): void
    {
        $list = $this->createList();
        $newComparator = new IntComparator();

        $list->setComparator($newComparator);

        $this->assertSame($newComparator, $list->getComparator());
        $this->assertNotSame($this->comparator, $list->getComparator());
    }

    #[Test]
    #[TestDox('ResortWithComparator reorders list with new comparator')]
    public function resortWithComparator_ReordersList_WithNewComparator(): void
    {
        $list = $this->createList();
        $list->addMultiple(1, 2, 3, 4, 5);

        $reverseComparator = new ReverseComparator($this->comparator);
        $list->setComparator($reverseComparator);

        $this->assertSame([5, 4, 3, 2, 1], iterator_to_array($list));
        $this->assertSame(5, $list->first());
        $this->assertSame(1, $list->last());
    }

    #[Test]
    #[TestDox('ResortWithComparator works with duplicates')]
    public function resortWithComparator_Works_WithDuplicates(): void
    {
        $list = $this->createList(allowDuplicates: true);
        $list->addMultiple(1, 2, 2, 3);

        $reverseComparator = new ReverseComparator($this->comparator);
        $list->setComparator($reverseComparator);

        $this->assertSame([3, 2, 2, 1], iterator_to_array($list));
    }

    #[Test]
    #[TestDox('ResortWithComparator preserves all elements')]
    public function resortWithComparator_PreservesAllElements(): void
    {
        $list = $this->createList();
        $list->addMultiple(5, 3, 1, 4, 2);

        $originalCount = $list->count();
        $originalValues = iterator_to_array($list);

        $reverseComparator = new ReverseComparator($this->comparator);
        $list->setComparator($reverseComparator);

        $this->assertSame($originalCount, $list->count());
        $newValues = iterator_to_array($list);
        $this->assertCount($originalCount, $newValues);
        $this->assertSame(array_reverse($originalValues), $newValues);
    }

    #[Test]
    #[TestDox('Handles large number of elements')]
    public function largeDataset_HandlesCorrectly(): void
    {
        $list = $this->createList();

        $values = range(1000, 1);
        $list->addMultiple(...$values);

        $this->assertCount(1000, $list);
        $this->assertSame(1, $list->first());
        $this->assertSame(1000, $list->last());

        $expected = range(1, 1000);
        $this->assertSame($expected, iterator_to_array($list));
    }

    #[Test]
    #[TestDox('Handles PHP_INT_MIN and PHP_INT_MAX together')]
    public function intMinAndMax_HandlesCorrectly(): void
    {
        $list = $this->createList();
        $list->addMultiple(PHP_INT_MIN, 0, PHP_INT_MAX);

        $this->assertCount(3, $list);
        $this->assertSame(PHP_INT_MIN, $list->first());
        $this->assertSame(PHP_INT_MAX, $list->last());
        $this->assertSame([PHP_INT_MIN, 0, PHP_INT_MAX], iterator_to_array($list));
    }

    #[Test]
    #[TestDox('Handles alternating insertions')]
    public function alternatingInsertions_MaintainsOrder(): void
    {
        $list = $this->createList();

        for ($i = 0; $i < 10; $i++) {
            $list->add($i);
            $list->add(1000 + $i);
        }

        $expected = array_merge(range(0, 9), range(1000, 1009));
        $this->assertSame($expected, iterator_to_array($list));
    }

    #[Test]
    #[TestDox('FindInRange with INT_MIN and INT_MAX boundaries')]
    public function findInRange_WithIntBoundaries_WorksCorrectly(): void
    {
        $list = $this->createList();
        $list->addMultiple(PHP_INT_MIN, -100, 0, 100, PHP_INT_MAX);

        $result = $list->findInRange(PHP_INT_MIN, PHP_INT_MAX);

        $this->assertSame([PHP_INT_MIN, -100, 0, 100, PHP_INT_MAX], iterator_to_array($result));
    }

    #[Test]
    #[TestDox('FindGreaterThan returns values greater than or equal to min')]
    public function findGreaterThan_ReturnsValues_GreaterThanOrEqualToMin(): void
    {
        $list = $this->createList();
        $list->addMultiple(1, 3, 5, 7, 9);

        $result = $list->findGreaterThan(5);

        $this->assertSame([5, 7, 9], iterator_to_array($result));
    }

    #[Test]
    #[TestDox('FindGreaterThan with exclusive boundary')]
    public function findGreaterThan_WithExclusiveBoundary_ReturnsValuesGreaterThanMin(): void
    {
        $list = $this->createList();
        $list->addMultiple(1, 3, 5, 7, 9);

        $result = $list->findGreaterThan(5, BetweenBoundary::EXCLUSIVE);

        $this->assertSame([7, 9], iterator_to_array($result));
    }

    #[Test]
    #[TestDox('FindLessThan returns values less than or equal to max')]
    public function findLessThan_ReturnsValues_LessThanOrEqualToMax(): void
    {
        $list = $this->createList();
        $list->addMultiple(1, 3, 5, 7, 9);

        $result = $list->findLessThan(5);

        $this->assertSame([1, 3, 5], iterator_to_array($result));
    }

    #[Test]
    #[TestDox('FindLessThan with exclusive boundary')]
    public function findLessThan_WithExclusiveBoundary_ReturnsValuesLessThanMax(): void
    {
        $list = $this->createList();
        $list->addMultiple(1, 3, 5, 7, 9);

        $result = $list->findLessThan(5, BetweenBoundary::EXCLUSIVE);

        $this->assertSame([1, 3], iterator_to_array($result));
    }

    #[Test]
    #[TestDox('FindInRange throws exception when min is greater than max')]
    public function findInRange_ThrowsException_WhenMinGreaterThanMax(): void
    {
        $list = $this->createList();
        $list->addMultiple(1, 2, 3);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid range: lower bound must come before upper bound');

        iterator_to_array($list->findInRange(10, 5));
    }

    #[Test]
    #[TestDox('FindGreaterThan/FindLessThan with INT_MIN/INT_MAX')]
    public function findGreaterThanAndLessThan_WithIntBoundaries_WorksCorrectly(): void
    {
        $list = $this->createList();
        $list->addMultiple(PHP_INT_MIN, -100, 0, 100, PHP_INT_MAX);

        $greaterThanResult = $list->findGreaterThan(0);
        $lessThanResult = $list->findLessThan(0);

        $this->assertSame([0, 100, PHP_INT_MAX], iterator_to_array($greaterThanResult));
        $this->assertSame([PHP_INT_MIN, -100, 0], iterator_to_array($lessThanResult));
    }

    #[Test]
    #[TestDox('Contains works with extreme values')]
    public function contains_WithExtremeValues_WorksCorrectly(): void
    {
        $list = $this->createList();
        $list->addMultiple(PHP_INT_MIN, 0, PHP_INT_MAX);

        $this->assertTrue($list->contains(PHP_INT_MIN));
        $this->assertTrue($list->contains(0));
        $this->assertTrue($list->contains(PHP_INT_MAX));
        $this->assertFalse($list->contains(PHP_INT_MIN + 1));
        $this->assertFalse($list->contains(PHP_INT_MAX - 1));
    }

    #[Test]
    #[TestDox('Remove and re-add maintains order')]
    public function removeAndReAdd_MaintainsOrder(): void
    {
        $list = $this->createList();
        $list->addMultiple(1, 2, 3, 4, 5);

        $list->remove(3);
        $list->add(3);

        $this->assertSame([1, 2, 3, 4, 5], iterator_to_array($list));
    }

    #[Test]
    #[TestDox('Multiple resort operations work correctly')]
    public function multipleResort_OperationsWork(): void
    {
        $list = $this->createList();
        $list->addMultiple(1, 2, 3, 4, 5);

        $reverseComparator = new ReverseComparator($this->comparator);
        $list->setComparator($reverseComparator);
        $this->assertSame([5, 4, 3, 2, 1], iterator_to_array($list));

        $list->setComparator($this->comparator);
        $this->assertSame([1, 2, 3, 4, 5], iterator_to_array($list));
    }

    #[Test]
    #[TestDox('Throws exception when float value is provided')]
    public function add_ThrowsException_WhenFloatValueProvided(): void
    {
        $list = $this->createList();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        $list->add(3.14);
    }

    #[Test]
    #[TestDox('AddMultiple throws exception when any value is float')]
    public function addMultiple_ThrowsException_WhenAnyValueIsFloat(): void
    {
        $list = $this->createList();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        $list->addMultiple(1, 2, 3.5, 4);
    }

    #[Test]
    #[TestDox('Remove throws exception when float value is provided')]
    public function remove_ThrowsException_WhenFloatValueProvided(): void
    {
        $list = $this->createList();
        $list->add(5);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        $list->remove(5.0);
    }

    #[Test]
    #[TestDox('Contains throws exception when float value is provided')]
    public function contains_ThrowsException_WhenFloatValueProvided(): void
    {
        $list = $this->createList();
        $list->add(5);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        $list->contains(5.0);
    }

    #[Test]
    #[TestDox('FindGreaterThan throws exception when float value is provided')]
    public function findGreaterThan_ThrowsException_WhenFloatValueProvided(): void
    {
        $list = $this->createList();
        $list->add(5);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        iterator_to_array($list->findGreaterThan(3.5));
    }

    #[Test]
    #[TestDox('FindLessThan throws exception when float value is provided')]
    public function findLessThan_ThrowsException_WhenFloatValueProvided(): void
    {
        $list = $this->createList();
        $list->add(5);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        iterator_to_array($list->findLessThan(10.0));
    }

    #[Test]
    #[TestDox('FindInRange throws exception when min is float')]
    public function findInRange_ThrowsException_WhenMinIsFloat(): void
    {
        $list = $this->createList();
        $list->add(5);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        iterator_to_array($list->findInRange(1.5, 10));
    }

    #[Test]
    #[TestDox('FindInRange throws exception when max is float')]
    public function findInRange_ThrowsException_WhenMaxIsFloat(): void
    {
        $list = $this->createList();
        $list->add(5);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        iterator_to_array($list->findInRange(1, 10.5));
    }

    #[Test]
    #[TestDox('RemoveAll throws exception when float value is provided')]
    public function removeAll_ThrowsException_WhenFloatValueProvided(): void
    {
        $list = $this->createList();
        $list->addMultiple(5, 5, 5);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        $list->removeAll(5.0);
    }

    #[Test]
    #[TestDox('Handles int overflow when value exceeds PHP_INT_MAX')]
    public function intOverflow_WhenValueExceedsIntMax_ThrowsException(): void
    {
        $list = $this->createList();

        $overflowValue = PHP_INT_MAX + 1;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        $list->add($overflowValue);
    }

    #[Test]
    #[TestDox('Handles int underflow when value is less than PHP_INT_MIN')]
    public function intUnderflow_WhenValueLessThanIntMin_ThrowsException(): void
    {
        $list = $this->createList();

        $underflowValue = PHP_INT_MIN - 1;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        $list->add($underflowValue);
    }

    #[Test]
    #[TestDox('AddMultiple rejects values that overflow int range')]
    public function addMultiple_RejectsValues_ThatOverflowIntRange(): void
    {
        $list = $this->createList();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        $list->addMultiple(1, 2, PHP_INT_MAX + 1, 4);
    }

    #[Test]
    #[TestDox('AddMultiple rejects values that underflow int range')]
    public function addMultiple_RejectsValues_ThatUnderflowIntRange(): void
    {
        $list = $this->createList();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer, float given');

        $list->addMultiple(1, 2, PHP_INT_MIN - 1, 4);
    }

    #[Test]
    #[TestDox('Valid int at boundary values work correctly')]
    public function boundaryIntValues_WorkCorrectly(): void
    {
        $list = $this->createList();

        $list->addMultiple(PHP_INT_MIN, -1, 0, 1, PHP_INT_MAX);

        $this->assertCount(5, $list);
        $this->assertSame(PHP_INT_MIN, $list->first());
        $this->assertSame(PHP_INT_MAX, $list->last());
        $this->assertTrue($list->contains(PHP_INT_MIN));
        $this->assertTrue($list->contains(PHP_INT_MAX));
        $this->assertTrue($list->contains(0));
    }

    #[Test]
    #[TestDox('Large integer within range works correctly')]
    public function largeIntWithinRange_WorksCorrectly(): void
    {
        $list = $this->createList();

        $largePositive = PHP_INT_MAX - 1000;
        $largeNegative = PHP_INT_MIN + 1000;

        $list->addMultiple($largePositive, $largeNegative, 0);

        $this->assertCount(3, $list);
        $this->assertSame($largeNegative, $list->first());
        $this->assertSame($largePositive, $list->last());
    }
}
