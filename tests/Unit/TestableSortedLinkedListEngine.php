<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit;

use HrjSnz\SortedLinkedList\BetweenBoundary;
use HrjSnz\SortedLinkedList\Comparator\IntComparator;
use HrjSnz\SortedLinkedList\SortedLinkedListEngine;

/**
 * @template T
 * @extends SortedLinkedListEngine<int>
 */
final class TestableSortedLinkedListEngine extends SortedLinkedListEngine
{
    public function __construct(IntComparator $comparator, bool $allowDuplicates = true)
    {
        parent::__construct($comparator, $allowDuplicates);
    }

    /** @param iterable<int> $values */
    public function testAddAll(iterable $values): void
    {
        $this->addAll($values);
    }

    /** @param int $value */
    public function testInsert(mixed $value): void
    {
        $this->insert($value);
    }

    /** @param int $value */
    public function testDelete(mixed $value): bool
    {
        return $this->delete($value);
    }

    /** @param int $value */
    public function testDeleteAll(mixed $value): int
    {
        return $this->deleteAll($value);
    }

    /** @param int $min */
    public function testGreaterThan(
        mixed $min,
        BetweenBoundary $boundary = BetweenBoundary::INCLUSIVE,
    ): \Generator {
        return $this->greaterThan($min, $boundary);
    }

    /** @param int $max */
    public function testLessThan(
        mixed $max,
        BetweenBoundary $boundary = BetweenBoundary::INCLUSIVE,
    ): \Generator {
        return $this->lessThan($max, $boundary);
    }

    /** @param int $min */
    /** @param int $max */
    public function testInRange(
        mixed $min,
        mixed $max,
        BetweenBoundary $minBoundary = BetweenBoundary::INCLUSIVE,
        BetweenBoundary $maxBoundary = BetweenBoundary::INCLUSIVE,
    ): \Generator {
        return $this->inRange($min, $max, $minBoundary, $maxBoundary);
    }

    /** @param int $value */
    public function testHas(mixed $value): bool
    {
        return $this->has($value);
    }
}
