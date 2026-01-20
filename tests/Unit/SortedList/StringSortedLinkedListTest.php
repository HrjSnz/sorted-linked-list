<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\SortedList;

use HrjSnz\SortedLinkedList\Comparator\StringComparator;
use HrjSnz\SortedLinkedList\SortedList\StringSortedLinkedList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(StringSortedLinkedList::class)]
final class StringSortedLinkedListTest extends TestCase
{
    use StringSortedLinkedListTestCaseTrait;

    protected function createComparator(bool $caseSensitive = true): StringComparator
    {
        return new StringComparator($caseSensitive);
    }


    #[Test]
    #[TestDox('Case-sensitive: Apple and apple are different')]
    public function caseSensitive_appleAndAppleAreDifferent(): void
    {
        $list = $this->createList(allowDuplicates: true, caseSensitive: true);
        $list->addMultiple('Apple', 'apple', 'APPLE');

        $this->assertSame(['APPLE', 'Apple', 'apple'], iterator_to_array($list));
        $this->assertCount(3, $list);
    }

    #[Test]
    #[TestDox('Case-insensitive: Apple and apple are the same')]
    public function caseInsensitive_appleAndAppleAreSame(): void
    {
        $list = $this->createList(allowDuplicates: false, caseSensitive: false);
        $list->addMultiple('Apple', 'apple', 'APPLE');

        $this->assertCount(1, $list);
    }

    #[Test]
    #[TestDox('Case-insensitive with duplicates allowed keeps first')]
    public function caseInsensitive_withDuplicatesAllowed_KeepsFirst(): void
    {
        $list = $this->createList(allowDuplicates: true, caseSensitive: false);
        $list->addMultiple('Apple', 'banana', 'apple', 'APPLE');

        $values = iterator_to_array($list);

        $this->assertContains('Apple', $values);
        $this->assertContains('banana', $values);
    }

    #[Test]
    #[TestDox('Case-sensitive sorting order')]
    public function caseSensitive_sortingOrder(): void
    {
        $list = $this->createList(caseSensitive: true);
        $list->addMultiple('zebra', 'Apple', 'banana', 'Zebra');

        $values = iterator_to_array($list);

        $this->assertLessThan(array_search('banana', $values), array_search('Apple', $values));
    }

    #[Test]
    #[TestDox('Case-insensitive contains')]
    public function caseInsensitive_contains(): void
    {
        $list = $this->createList(caseSensitive: false);
        $list->add('Apple');

        $this->assertTrue($list->contains('apple'));
        $this->assertTrue($list->contains('APPLE'));
        $this->assertTrue($list->contains('Apple'));
    }

    #[Test]
    #[TestDox('Case-sensitive contains')]
    public function caseSensitive_contains(): void
    {
        $list = $this->createList(caseSensitive: true);
        $list->add('Apple');

        $this->assertTrue($list->contains('Apple'));
        $this->assertFalse($list->contains('apple'));
        $this->assertFalse($list->contains('APPLE'));
    }
}
