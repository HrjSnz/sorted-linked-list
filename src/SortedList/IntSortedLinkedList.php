<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\SortedList;

use Generator;
use InvalidArgumentException;
use HrjSnz\SortedLinkedList\BetweenBoundary;
use HrjSnz\SortedLinkedList\Comparator\ComparatorInterface;
use HrjSnz\SortedLinkedList\SortedLinkedListEngine;

/**
 * @extends SortedLinkedListEngine<int>
 */
final class IntSortedLinkedList extends SortedLinkedListEngine
{
    /**
     * @param ComparatorInterface<int> $comparator
     */
    public function __construct(ComparatorInterface $comparator, bool $allowDuplicates = true)
    {
        parent::__construct($comparator, $allowDuplicates);
    }

    public function add(int|float $value): void
    {
        $value = $this->ensureIntType($value);

        parent::insert($value);
    }

    public function remove(int|float $value): bool
    {
        $value = $this->ensureIntType($value);

        return parent::delete($value);
    }

    public function contains(int|float  $value): bool
    {
        $value = $this->ensureIntType($value);

        return parent::has($value);
    }

    public function addMultiple(int|float ...$values): void
    {
        $checked = [];
        foreach ($values as $value) {
            $checked[] = $this->ensureIntType($value);
        }

        parent::addAll($checked);
    }

    public function findGreaterThan(
        int|float             $min,
        BetweenBoundary $boundary = BetweenBoundary::INCLUSIVE,
    ): Generator {
        $min = $this->ensureIntType($min);

        return parent::greaterThan($min, $boundary);
    }

    public function findLessThan(
        int|float             $max,
        BetweenBoundary $boundary = BetweenBoundary::INCLUSIVE,
    ): Generator {
        $max = $this->ensureIntType($max);

        return parent::lessThan($max, $boundary);
    }

    public function findInRange(
        int|float             $min,
        int|float             $max,
        BetweenBoundary $minBoundary = BetweenBoundary::INCLUSIVE,
        BetweenBoundary $maxBoundary = BetweenBoundary::INCLUSIVE,
    ): Generator {
        $min = $this->ensureIntType($min);
        $max = $this->ensureIntType($max);

        return parent::inRange($min, $max, $minBoundary, $maxBoundary);
    }

    public function removeAll(int|float $value): int
    {
        $value = $this->ensureIntType($value);

        return parent::deleteAll($value);
    }

    // handle PHP_INT_MIN PHP_INT_MAX exceed
    private function ensureIntType(int|float $value): int
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException(
                sprintf('Value must be an integer, float given: %s', var_export($value, true))
            );
        }

        return $value;
    }
}
