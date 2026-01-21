<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\SortedList;

use HrjSnz\SortedLinkedList\BetweenBoundary;
use HrjSnz\SortedLinkedList\Comparator\ComparatorInterface;
use HrjSnz\SortedLinkedList\SortedLinkedListEngine;

/**
 * @extends SortedLinkedListEngine<string>
 */
final class StringSortedLinkedList extends SortedLinkedListEngine
{
    /**
     * @param ComparatorInterface<string> $comparator
     */
    public function __construct(ComparatorInterface $comparator, bool $allowDuplicates = true)
    {
        parent::__construct($comparator, $allowDuplicates);
    }

    public function add(string $value): void
    {
        parent::insert($value);
    }

    public function remove(string $value): bool
    {
        return parent::delete($value);
    }

    public function contains(string $value): bool
    {
        return parent::has($value);
    }

    public function addMultiple(string ...$values): void
    {
        parent::addAll($values);
    }

    /**
     * @return \Traversable<int, string>
     */
    public function findGreaterThan(
        string          $min,
        BetweenBoundary $boundary = BetweenBoundary::INCLUSIVE,
    ): \Traversable {
        return parent::greaterThan($min, $boundary);
    }

    /**
     * @return \Traversable<int, string>
     */
    public function findLessThan(
        string          $max,
        BetweenBoundary $boundary = BetweenBoundary::INCLUSIVE,
    ): \Traversable {
        return parent::lessThan($max, $boundary);
    }

    /**
     * @return \Traversable<int, string>
     */
    public function findInRange(
        string          $min,
        string          $max,
        BetweenBoundary $minBoundary = BetweenBoundary::INCLUSIVE,
        BetweenBoundary $maxBoundary = BetweenBoundary::INCLUSIVE,
    ): \Traversable {
        return parent::inRange($min, $max, $minBoundary, $maxBoundary);
    }

    public function removeAll(string $value): int
    {
        return parent::deleteAll($value);
    }
}
