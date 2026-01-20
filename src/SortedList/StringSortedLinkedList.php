<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\SortedList;

use Generator;
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

    public function findGreaterThan(
        string          $min,
        BetweenBoundary $boundary = BetweenBoundary::INCLUSIVE,
    ): Generator {
        return parent::greaterThan($min, $boundary);
    }

    public function findLessThan(
        string          $max,
        BetweenBoundary $boundary = BetweenBoundary::INCLUSIVE,
    ): Generator {
        return parent::lessThan($max, $boundary);
    }

    public function findInRange(
        string          $min,
        string          $max,
        BetweenBoundary $minBoundary = BetweenBoundary::INCLUSIVE,
        BetweenBoundary $maxBoundary = BetweenBoundary::INCLUSIVE,
    ): Generator {
        return parent::inRange($min, $max, $minBoundary, $maxBoundary);
    }

    public function removeAll(string $value): int
    {
        return parent::deleteAll($value);
    }
}
