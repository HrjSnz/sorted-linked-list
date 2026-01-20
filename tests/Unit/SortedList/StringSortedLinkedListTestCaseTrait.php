<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\SortedList;

use HrjSnz\SortedLinkedList\BetweenBoundary;
use HrjSnz\SortedLinkedList\Comparator\ComparatorInterface;
use HrjSnz\SortedLinkedList\SortedList\StringSortedLinkedList;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

trait StringSortedLinkedListTestCaseTrait
{
    /**
     * @return ComparatorInterface<string>
     */
    abstract protected function createComparator(bool $caseSensitive = true): ComparatorInterface;

    protected function createList(bool $allowDuplicates = true, bool $caseSensitive = true): StringSortedLinkedList
    {
        return new StringSortedLinkedList($this->createComparator($caseSensitive), $allowDuplicates);
    }

    #[Test]
    #[TestDox('List is empty when created')]
    public function constructor_createsEmptyList(): void
    {
        $list = $this->createList();

        $this->assertTrue($list->isEmpty());
        $this->assertCount(0, $list);
    }

    #[Test]
    #[TestDox('First and last are null when list is empty')]
    public function constructor_firstAndLastReturnNull(): void
    {
        $list = $this->createList();

        $this->assertNull($list->first());
        $this->assertNull($list->last());
    }

    #[Test]
    #[TestDox('Count returns zero when list is empty')]
    public function constructor_countReturnsZero(): void
    {
        $list = $this->createList();

        $this->assertSame(0, $list->count());
        $this->assertCount(0, $list);
    }

    #[Test]
    #[TestDox('IsEmpty returns true when list is empty')]
    public function constructor_isEmptyReturnsTrue(): void
    {
        $list = $this->createList();

        $this->assertTrue($list->isEmpty());
    }

    /**
     * @param array<string> $initialValues
     * @param array<string> $expectedValues
     */
    #[Test]
    #[DataProvider('provideSingleAddCases')]
    #[TestDox('Add single value to list in sorted order')]
    public function add_InsertsValue_InSortedOrder(
        array $initialValues,
        string $valueToAdd,
        array $expectedValues
    ): void {
        $list = $this->createList();

        foreach ($initialValues as $value) {
            $list->add($value);
        }

        $list->add($valueToAdd);

        $this->assertSame($expectedValues, iterator_to_array($list));
    }

    /**
     * @return array<string, array{array<string>, string, array<string>}>
     */
    public static function provideSingleAddCases(): array
    {
        return [
            'add_to_empty' => [[], 'banana', ['banana']],
            'add_to_head_alphabetically_first' => [['cherry', 'date'], 'apple', ['apple', 'cherry', 'date']],
            'add_to_tail_alphabetically_last' => [['apple', 'banana'], 'cherry', ['apple', 'banana', 'cherry']],
            'add_in_middle' => [['apple', 'cherry'], 'banana', ['apple', 'banana', 'cherry']],
            'add_empty_string_to_empty' => [[], '', ['']],
            'add_empty_string_to_nonempty' => [['a', 'b'], '', ['', 'a', 'b']],
            'add_single_character' => [[], 'x', ['x']],
            'add_to_reverse_sorted' => [['date', 'cherry', 'banana'], 'apple', ['apple', 'banana', 'cherry', 'date']],
            'case_sensitive_apple_before_banana' => [[], 'apple', ['apple']],
            'case_sensitive_Banana_before_apple' => [[], 'Banana', ['Banana']],
        ];
    }

    #[Test]
    #[TestDox('Allows duplicates when allowDuplicates is true')]
    public function add_AllowsDuplicates_WhenModeEnabled(): void
    {
        $list = $this->createList(allowDuplicates: true);

        $list->add('apple');
        $list->add('banana');
        $list->add('apple');
        $list->add('apple');

        $this->assertSame(['apple', 'apple', 'apple', 'banana'], iterator_to_array($list));
        $this->assertCount(4, $list);
    }

    #[Test]
    #[TestDox('Rejects duplicates when allowDuplicates is false')]
    public function add_RejectsDuplicates_WhenModeDisabled(): void
    {
        $list = $this->createList(allowDuplicates: false);

        $list->add('apple');
        $list->add('banana');
        $list->add('apple');
        $list->add('apple');

        $this->assertSame(['apple', 'banana'], iterator_to_array($list));
        $this->assertCount(2, $list);
    }

    #[Test]
    #[TestDox('RemoveAll removes all occurrences when duplicates enabled')]
    public function removeAll_RemovesAllOccurrences_WhenDuplicatesEnabled(): void
    {
        $list = $this->createList(allowDuplicates: true);
        $list->addMultiple('apple', 'banana', 'apple', 'apple', 'banana');

        $count = $list->removeAll('apple');

        $this->assertSame(3, $count);
        $this->assertSame(['banana', 'banana'], iterator_to_array($list));
    }

    #[Test]
    #[TestDox('RemoveAll removes one occurrence when duplicates disabled')]
    public function removeAll_RemovesOneOccurrence_WhenDuplicatesDisabled(): void
    {
        $list = $this->createList(allowDuplicates: false);
        $list->addMultiple('apple', 'banana', 'apple', 'apple', 'banana');

        $count = $list->removeAll('apple');

        $this->assertSame(1, $count);
        $this->assertSame(['banana'], iterator_to_array($list));
    }

    #[Test]
    #[TestDox('GetDuplicates returns array of duplicated values')]
    public function getDuplicates_ReturnsArray_WithDuplicateValues(): void
    {
        $list = $this->createList(allowDuplicates: true);
        $list->addMultiple('apple', 'banana', 'apple', 'cherry', 'banana', 'apple');

        $duplicates = $list->getDuplicates();

        $this->assertSame(['apple', 'banana'], $duplicates);
    }

    #[Test]
    #[TestDox('GetDuplicatesWithCount returns array with value and count')]
    public function getDuplicatesWithCount_ReturnsArray_WithValueAndCount(): void
    {
        $list = $this->createList(allowDuplicates: true);
        $list->addMultiple('apple', 'banana', 'apple', 'apple', 'cherry');

        $result = $list->getDuplicatesWithCount();

        $this->assertSame([
            ['value' => 'apple', 'count' => 3],
        ], $result);
    }

    /**
     * @param array<string> $initialValues
     * @param array<string> $valuesToAdd
     * @param array<string> $expectedValues
     */
    #[Test]
    #[DataProvider('provideAddMultipleCases')]
    #[TestDox('AddMultiple maintains sorted order')]
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

    /**
     * @return array<string, array{array<string>, array<string>, array<string>}>
     */
    public static function provideAddMultipleCases(): array
    {
        return [
            'add_to_empty' => [[], ['cherry', 'apple', 'banana'], ['apple', 'banana', 'cherry']],
            'add_already_sorted' => [['apple', 'banana'], ['cherry', 'date'], ['apple', 'banana', 'cherry', 'date']],
            'add_reverse_sorted' => [[], ['date', 'cherry', 'banana', 'apple'], ['apple', 'banana', 'cherry', 'date']],
            'add_random_order' => [['banana'], ['apple', 'cherry'], ['apple', 'banana', 'cherry']],
            'add_with_duplicates_enabled' => [[], ['apple', 'banana', 'apple'], ['apple', 'apple', 'banana']],
            'add_empty_array' => [['apple', 'banana'], [], ['apple', 'banana']],
            'add_single_value' => [[], ['zebra'], ['zebra']],
        ];
    }

    #[Test]
    #[TestDox('AddMultiple rejects duplicates when mode disabled')]
    public function addMultiple_RejectsDuplicates_WhenModeDisabled(): void
    {
        $list = $this->createList(allowDuplicates: false);

        $list->addMultiple('apple', 'banana', 'apple', 'cherry', 'banana');

        $this->assertSame(['apple', 'banana', 'cherry'], iterator_to_array($list));
    }

    /**
     * @param array<string> $values
     */
    #[Test]
    #[DataProvider('provideContainsCases')]
    #[TestDox('Contains returns correct boolean')]
    public function contains_ReturnsCorrectBoolean(
        array $values,
        string $searchValue,
        bool $expected
    ): void {
        $list = $this->createList();
        $list->addMultiple(...$values);

        $result = $list->contains($searchValue);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{array<string>, string, bool}>
     */
    public static function provideContainsCases(): array
    {
        return [
            'exists_in_single_element' => [['apple'], 'apple', true],
            'exists_in_middle' => [['apple', 'banana', 'cherry'], 'banana', true],
            'exists_at_head' => [['apple', 'banana', 'cherry'], 'apple', true],
            'exists_at_tail' => [['apple', 'banana', 'cherry'], 'cherry', true],
            'not_exists_smaller' => [['banana', 'cherry'], 'apple', false],
            'not_exists_larger' => [['apple', 'banana'], 'cherry', false],
            'not_exists_between' => [['apple', 'cherry'], 'banana', false],
            'empty_list' => [[], 'apple', false],
            'empty_string_exists' => [['', 'a', 'b'], '', true],
            'empty_string_not_exists' => [['a', 'b'], '', false],
        ];
    }

    /**
     * @param array<string> $values
     * @param array<string> $expectedValues
     */
    #[Test]
    #[DataProvider('provideRemoveCases')]
    #[TestDox('Remove returns success status and updates list')]
    public function remove_RemovesValue_ReturnsSuccess(
        array $values,
        string $valueToRemove,
        bool $expectedResult,
        array $expectedValues
    ): void {
        $list = $this->createList();
        $list->addMultiple(...$values);

        $result = $list->remove($valueToRemove);

        $this->assertSame($expectedResult, $result);
        $this->assertSame($expectedValues, iterator_to_array($list));
    }

    /**
     * @return array<string, array{array<string>, string, bool, array<string>}>
     */
    public static function provideRemoveCases(): array
    {
        return [
            'remove_from_single_element' => [['apple'], 'apple', true, []],
            'remove_from_head' => [['apple', 'banana', 'cherry'], 'apple', true, ['banana', 'cherry']],
            'remove_from_tail' => [['apple', 'banana', 'cherry'], 'cherry', true, ['apple', 'banana']],
            'remove_from_middle' => [['apple', 'banana', 'cherry'], 'banana', true, ['apple', 'cherry']],
            'remove_not_exists' => [['apple', 'banana'], 'cherry', false, ['apple', 'banana']],
            'remove_from_empty' => [[], 'apple', false, []],
            'remove_empty_string' => [['', 'apple'], '', true, ['apple']],
        ];
    }

    #[Test]
    #[TestDox('Remove only removes first occurrence when duplicates enabled')]
    public function remove_RemovesOnlyFirstOccurrence_WhenDuplicatesEnabled(): void
    {
        $list = $this->createList(allowDuplicates: true);
        $list->addMultiple('apple', 'banana', 'apple', 'apple', 'banana');

        $result = $list->remove('apple');

        $this->assertTrue($result);
        $this->assertSame(['apple', 'apple', 'banana', 'banana'], iterator_to_array($list));
        $this->assertCount(4, $list);
    }

    #[Test]
    #[TestDox('Remove returns false when duplicates disabled and value not found')]
    public function remove_ReturnsFalse_WhenDuplicatesDisabledAndNotFound(): void
    {
        $list = $this->createList(allowDuplicates: false);
        $list->addMultiple('apple', 'banana', 'cherry');

        $result = $list->remove('date');

        $this->assertFalse($result);
    }

    /**
     * @param array<string> $values
     * @param array<string> $expectedValues
     */
    #[Test]
    #[DataProvider('provideRemoveAllCases')]
    #[TestDox('RemoveAll removes occurrences and returns count')]
    public function removeAll_RemovesOccurrences_ReturnsCount(
        bool $allowDuplicates,
        array $values,
        string $valueToRemove,
        int $expectedCount,
        array $expectedValues
    ): void {
        $list = $this->createList(allowDuplicates: $allowDuplicates);
        $list->addMultiple(...$values);

        $count = $list->removeAll($valueToRemove);

        $this->assertSame($expectedCount, $count);
        $this->assertSame($expectedValues, iterator_to_array($list));
    }

    /**
     * @return array<string, array{bool, array<string>, string, int, array<string>}>
     */
    public static function provideRemoveAllCases(): array
    {
        return [
            'remove_all_duplicates_enabled' => [true, ['apple', 'banana', 'apple', 'apple', 'banana'], 'apple', 3, ['banana', 'banana']],
            'remove_all_duplicates_disabled' => [false, ['apple', 'banana', 'apple', 'apple', 'banana'], 'apple', 1, ['banana']],
            'remove_single_occurrence' => [true, ['apple', 'banana', 'cherry'], 'banana', 1, ['apple', 'cherry']],
            'remove_not_exists' => [true, ['apple', 'banana'], 'cherry', 0, ['apple', 'banana']],
            'remove_from_empty' => [true, [], 'apple', 0, []],
            'remove_all_elements' => [true, ['apple', 'apple', 'apple'], 'apple', 3, []],
        ];
    }

    /**
     * @param array<string> $values
     * @param array<string> $expected
     */
    #[Test]
    #[DataProvider('provideFindInRangeCases')]
    #[TestDox('FindInRange returns values within boundaries')]
    public function findInRange_ReturnsValues_WithinBoundaries(
        array $values,
        string $min,
        string $max,
        BetweenBoundary $minBoundary,
        BetweenBoundary $maxBoundary,
        array $expected
    ): void {
        $list = $this->createList();
        $list->addMultiple(...$values);

        $result = $list->findInRange($min, $max, $minBoundary, $maxBoundary);

        $this->assertSame($expected, iterator_to_array($result));
    }

    /**
     * @return array<string, array{array<string>, string, string, BetweenBoundary, BetweenBoundary, array<string>}>
     */
    public static function provideFindInRangeCases(): array
    {
        return [
            'both_inclusive' => [['apple', 'banana', 'cherry', 'date', 'elderberry'], 'banana', 'date', BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, ['banana', 'cherry', 'date']],
            'both_exclusive' => [['apple', 'banana', 'cherry', 'date', 'elderberry'], 'banana', 'date', BetweenBoundary::EXCLUSIVE, BetweenBoundary::EXCLUSIVE, ['cherry']],
            'min_exclusive_max_inclusive' => [['apple', 'banana', 'cherry', 'date'], 'banana', 'date', BetweenBoundary::EXCLUSIVE, BetweenBoundary::INCLUSIVE, ['cherry', 'date']],
            'min_inclusive_max_exclusive' => [['apple', 'banana', 'cherry', 'date'], 'banana', 'date', BetweenBoundary::INCLUSIVE, BetweenBoundary::EXCLUSIVE, ['banana', 'cherry']],
            'no_match' => [['apple', 'banana', 'cherry'], 'date', 'elderberry', BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, []],
            'duplicates_in_range' => [['apple', 'banana', 'banana', 'cherry'], 'banana', 'cherry', BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, ['banana', 'banana', 'cherry']],
            'empty_string_range' => [['', 'a', 'b'], '', 'a', BetweenBoundary::INCLUSIVE, BetweenBoundary::INCLUSIVE, ['', 'a']],
        ];
    }

    #[Test]
    #[TestDox('FindInRange throws exception when min is greater than max')]
    public function findInRange_ThrowsException_WhenMinGreaterThanMax(): void
    {
        $list = $this->createList();
        $list->addMultiple('apple', 'banana', 'cherry');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid range: lower bound must come before upper bound');

        iterator_to_array($list->findInRange('cherry', 'apple'));
    }

    #[Test]
    #[TestDox('FindGreaterThan returns values greater than or equal to min')]
    public function findGreaterThan_ReturnsValues_GreaterThanOrEqualToMin(): void
    {
        $list = $this->createList();
        $list->addMultiple('apple', 'banana', 'cherry', 'date', 'elderberry');

        $result = $list->findGreaterThan('cherry');

        $this->assertSame(['cherry', 'date', 'elderberry'], iterator_to_array($result));
    }

    #[Test]
    #[TestDox('FindGreaterThan with exclusive boundary')]
    public function findGreaterThan_WithExclusiveBoundary_ReturnsValuesGreaterThanMin(): void
    {
        $list = $this->createList();
        $list->addMultiple('apple', 'banana', 'cherry', 'date', 'elderberry');

        $result = $list->findGreaterThan('cherry', BetweenBoundary::EXCLUSIVE);

        $this->assertSame(['date', 'elderberry'], iterator_to_array($result));
    }

    #[Test]
    #[TestDox('FindGreaterThan with no matches')]
    public function findGreaterThan_WithNoMatches_ReturnsEmpty(): void
    {
        $list = $this->createList();
        $list->addMultiple('apple', 'banana');

        $result = $list->findGreaterThan('cherry');

        $this->assertSame([], iterator_to_array($result));
    }

    #[Test]
    #[TestDox('FindLessThan returns values less than or equal to max')]
    public function findLessThan_ReturnsValues_LessThanOrEqualToMax(): void
    {
        $list = $this->createList();
        $list->addMultiple('apple', 'banana', 'cherry', 'date', 'elderberry');

        $result = $list->findLessThan('cherry');

        $this->assertSame(['apple', 'banana', 'cherry'], iterator_to_array($result));
    }

    #[Test]
    #[TestDox('FindLessThan with exclusive boundary')]
    public function findLessThan_WithExclusiveBoundary_ReturnsValuesLessThanMax(): void
    {
        $list = $this->createList();
        $list->addMultiple('apple', 'banana', 'cherry', 'date', 'elderberry');

        $result = $list->findLessThan('cherry', BetweenBoundary::EXCLUSIVE);

        $this->assertSame(['apple', 'banana'], iterator_to_array($result));
    }

    #[Test]
    #[TestDox('FindLessThan with no matches')]
    public function findLessThan_WithNoMatches_ReturnsEmpty(): void
    {
        $list = $this->createList();
        $list->addMultiple('date', 'elderberry');

        $result = $list->findLessThan('cherry');

        $this->assertSame([], iterator_to_array($result));
    }

    /**
     * @param array<string> $values
     */
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

    /**
     * @return array<string, array{array<string>, int}>
     */
    public static function provideCountCases(): array
    {
        return [
            'empty_list' => [[], 0],
            'single_element' => [['apple'], 1],
            'multiple_elements' => [['apple', 'banana', 'cherry'], 3],
            'with_duplicates' => [['apple', 'apple', 'banana'], 3],
        ];
    }

    /**
     * @param array<string> $values
     */
    #[Test]
    #[DataProvider('provideIsEmptyCases')]
    #[TestDox('IsEmpty returns correct boolean')]
    public function isEmpty_ReturnsCorrectBoolean(array $values, bool $expected): void
    {
        $list = $this->createList();
        $list->addMultiple(...$values);

        $this->assertSame($expected, $list->isEmpty());
    }

    /**
     * @return array<string, array{array<string>, bool}>
     */
    public static function provideIsEmptyCases(): array
    {
        return [
            'empty_list' => [[], true],
            'single_element' => [['apple'], false],
            'multiple_elements' => [['apple', 'banana'], false],
        ];
    }

    /**
     * @param array<string> $values
     */
    #[Test]
    #[DataProvider('provideFirstLastCases')]
    #[TestDox('First and last return correct values')]
    public function firstAndLast_ReturnCorrectValues(
        array $values,
        ?string $expectedFirst,
        ?string $expectedLast
    ): void {
        $list = $this->createList();
        $list->addMultiple(...$values);

        $this->assertSame($expectedFirst, $list->first());
        $this->assertSame($expectedLast, $list->last());
    }

    /**
     * @return array<string, array{array<string>, ?string, ?string}>
     */
    public static function provideFirstLastCases(): array
    {
        return [
            'empty_list' => [[], null, null],
            'single_element' => [['apple'], 'apple', 'apple'],
            'multiple_elements' => [['apple', 'banana', 'cherry'], 'apple', 'cherry'],
            'reverse_insertion' => [['cherry', 'banana', 'apple'], 'apple', 'cherry'],
            'with_duplicates' => [['apple', 'apple', 'banana'], 'apple', 'banana'],
        ];
    }

    #[Test]
    #[TestDox('Iterate returns all values in sorted order')]
    public function getIterator_ReturnsAllValues_InSortedOrder(): void
    {
        $list = $this->createList();
        $list->addMultiple('cherry', 'apple', 'date', 'banana', 'elderberry');

        $values = iterator_to_array($list);

        $this->assertSame(['apple', 'banana', 'cherry', 'date', 'elderberry'], $values);
    }

    #[Test]
    #[TestDox('Handles accented Latin characters')]
    public function unicode_handlesAccentedLatin(): void
    {
        $list = $this->createList();
        $list->addMultiple('café', 'naïve', 'über', 'apple');

        $values = iterator_to_array($list);

        $this->assertContains('café', $values);
        $this->assertContains('naïve', $values);
        $this->assertContains('über', $values);
    }

    #[Test]
    #[TestDox('Handles Cyrillic characters')]
    public function unicode_handlesCyrillic(): void
    {
        $list = $this->createList();
        $list->addMultiple('Привет', 'мир', 'apple');

        $values = iterator_to_array($list);

        $this->assertContains('Привет', $values);
        $this->assertContains('мир', $values);
    }

    #[Test]
    #[TestDox('Handles Greek characters')]
    public function unicode_handlesGreek(): void
    {
        $list = $this->createList();
        $list->addMultiple('Ελλάδα', 'αυτό', 'beta');

        $values = iterator_to_array($list);

        $this->assertContains('Ελλάδα', $values);
        $this->assertContains('αυτό', $values);
    }

    #[Test]
    #[TestDox('Handles CJK characters')]
    public function unicode_handlesCJK(): void
    {
        $list = $this->createList();
        $list->addMultiple('中文', '日本語', '한국어', 'apple');

        $values = iterator_to_array($list);

        $this->assertContains('中文', $values);
        $this->assertContains('日本語', $values);
        $this->assertContains('한국어', $values);
    }

    #[Test]
    #[TestDox('Handles emoji')]
    public function unicode_handlesEmoji(): void
    {
        $list = $this->createList();
        $list->addMultiple('😀', '🎉', '🚀', 'apple');

        $values = iterator_to_array($list);

        $this->assertContains('😀', $values);
        $this->assertContains('🎉', $values);
        $this->assertContains('🚀', $values);
    }

    #[Test]
    #[TestDox('Handles Arabic text (RTL script)')]
    public function unicode_handlesArabic(): void
    {
        $list = $this->createList();
        $list->addMultiple('العربية', 'مرحبا', 'apple');

        $values = iterator_to_array($list);

        $this->assertContains('العربية', $values);
        $this->assertContains('مرحبا', $values);
    }

    #[Test]
    #[TestDox('Handles Hebrew text (RTL script)')]
    public function unicode_handlesHebrew(): void
    {
        $list = $this->createList();
        $list->addMultiple('עברית', 'שלום', 'apple');

        $values = iterator_to_array($list);

        $this->assertContains('עברית', $values);
        $this->assertContains('שלום', $values);
    }

    #[Test]
    #[TestDox('Handles very long strings')]
    public function stringLength_handlesVeryLongStrings(): void
    {
        $list = $this->createList();
        $longString = str_repeat('z', 10000);
        $longString2 = str_repeat('y', 10000);

        $list->addMultiple($longString, $longString2, 'apple');

        $this->assertCount(3, $list);
        $this->assertSame('apple', $list->first());
        $this->assertSame($longString, $list->last());
    }

    #[Test]
    #[TestDox('Handles whitespace variations')]
    public function whitespace_handlesVariations(): void
    {
        $list = $this->createList();
        $list->addMultiple('space', ' tab', 'newline\n', 'mixed \t\n');

        $values = iterator_to_array($list);

        $this->assertContains(' tab', $values);
        $this->assertContains('newline\n', $values);
        $this->assertContains('mixed \t\n', $values);
    }

    #[Test]
    #[TestDox('Leading and trailing whitespace is significant')]
    public function whitespace_leadingTrailingIsSignificant(): void
    {
        $list = $this->createList();
        $list->addMultiple('apple', ' apple', 'apple ', ' apple ');

        $this->assertCount(4, $list);
    }

    #[Test]
    #[TestDox('Handles only whitespace strings')]
    public function whitespace_onlyWhitespaceStrings(): void
    {
        $list = $this->createList();
        $list->addMultiple(' ', "\t", "\n", '  ');

        $this->assertCount(4, $list);
    }

    #[Test]
    #[TestDox('Handles strings with null bytes')]
    public function specialChars_handlesNullBytes(): void
    {
        $list = $this->createList();
        $stringWithNull = "apple\0banana";

        $list->add($stringWithNull);
        $list->add('cherry');

        $this->assertCount(2, $list);
        $this->assertTrue($list->contains($stringWithNull));
    }

    #[Test]
    #[TestDox('Handles quote characters')]
    public function specialChars_handlesQuotes(): void
    {
        $list = $this->createList();
        $list->addMultiple("single'quote", 'double"quote', '`backtick`');

        $this->assertCount(3, $list);
    }

    #[Test]
    #[TestDox('Handles multiline strings')]
    public function specialChars_handlesMultilineStrings(): void
    {
        $list = $this->createList();
        $multiline1 = "line1\nline2\nline3";
        $multiline2 = "line1\nline2";

        $list->addMultiple($multiline1, $multiline2, 'apple');

        $this->assertCount(3, $list);
    }

    #[Test]
    #[TestDox('Handles large number of elements')]
    public function stressTest_handlesLargeDataset(): void
    {
        $list = $this->createList();

        $values = [];
        for ($i = 999; $i >= 0; $i--) {
            $values[] = 'string-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT);
        }
        $list->addMultiple(...$values);

        $this->assertCount(1000, $list);
        $this->assertSame('string-0000', $list->first());
        $this->assertSame('string-0999', $list->last());

        $iterated = iterator_to_array($list);
        $expected = [];
        for ($i = 0; $i <= 999; $i++) {
            $expected[] = 'string-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT);
        }
        $this->assertSame($expected, $iterated);
    }

    #[Test]
    #[TestDox('Handles large dataset with many duplicates')]
    public function stressTest_handlesLargeDatasetWithManyDuplicates(): void
    {
        $list = $this->createList(allowDuplicates: true);

        $values = [];
        for ($i = 0; $i < 500; $i++) {
            $values[] = 'apple';
            $values[] = 'banana';
            $values[] = 'cherry';
        }
        $list->addMultiple(...$values);

        $this->assertCount(1500, $list);

        $duplicates = $list->getDuplicates();
        $this->assertSame(['apple', 'banana', 'cherry'], $duplicates);
    }

    #[Test]
    #[TestDox('Handles alternating insertions')]
    public function stressTest_handlesAlternatingInsertions(): void
    {
        $list = $this->createList();

        for ($i = 0; $i < 50; $i++) {
            $list->add("zzz-{$i}");
            $list->add("aaa-{$i}");
        }

        $this->assertCount(100, $list);

        $values = iterator_to_array($list);
        $this->assertStringStartsWith('aaa-', $values[0]);
        $this->assertStringStartsWith('zzz-', $values[99]);
    }

    #[Test]
    #[TestDox('Handle remove and re-add maintains order')]
    public function removeAndReAdd_maintainsOrder(): void
    {
        $list = $this->createList();
        $list->addMultiple('apple', 'banana', 'cherry', 'date', 'elderberry');

        $list->remove('cherry');
        $list->add('cherry');

        $this->assertSame(['apple', 'banana', 'cherry', 'date', 'elderberry'], iterator_to_array($list));
    }

    #[Test]
    #[TestDox('GetComparator returns the set comparator')]
    public function getComparator_ReturnsSetComparator(): void
    {
        $list = $this->createList();
        $comparator = $this->createComparator();
        $listComparator = $list->getComparator();

        $this->assertSame($comparator::class, $listComparator::class);
    }

    #[Test]
    #[TestDox('SetComparator changes the comparator')]
    public function setComparator_ChangesComparator(): void
    {
        $list = $this->createList();
        $newComparator = $this->createComparator();

        $list->setComparator($newComparator);

        $this->assertSame($newComparator, $list->getComparator());
    }

    #[Test]
    #[TestDox('ResortWithComparator reorders list with new comparator')]
    public function resortWithComparator_ReordersList_WithNewComparator(): void
    {
        $list = $this->createList();
        $list->addMultiple('apple', 'banana', 'cherry', 'date');

        $reverseComparator = new \HrjSnz\SortedLinkedList\Comparator\ReverseComparator($list->getComparator());
        $list->setComparator($reverseComparator);

        $this->assertSame(['date', 'cherry', 'banana', 'apple'], iterator_to_array($list));
    }

    #[Test]
    #[TestDox('ResortWithComparator works with duplicates')]
    public function resortWithComparator_Works_WithDuplicates(): void
    {
        $list = $this->createList(allowDuplicates: true);
        $list->addMultiple('apple', 'banana', 'banana', 'cherry');

        $reverseComparator = new \HrjSnz\SortedLinkedList\Comparator\ReverseComparator($list->getComparator());
        $list->setComparator($reverseComparator);

        $this->assertSame(['cherry', 'banana', 'banana', 'apple'], iterator_to_array($list));
    }

    #[Test]
    #[TestDox('ResortWithComparator preserves all elements')]
    public function resortWithComparator_PreservesAllElements(): void
    {
        $list = $this->createList();
        $list->addMultiple('elderberry', 'apple', 'date', 'banana', 'cherry');

        $originalCount = $list->count();
        $originalValues = iterator_to_array($list);

        $reverseComparator = new \HrjSnz\SortedLinkedList\Comparator\ReverseComparator($list->getComparator());
        $list->setComparator($reverseComparator);

        $this->assertSame($originalCount, $list->count());
        $newValues = iterator_to_array($list);
        $this->assertCount($originalCount, $newValues);
        $this->assertSame(array_reverse($originalValues), $newValues);
    }
}
