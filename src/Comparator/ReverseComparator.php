<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Comparator;

/**
 * @template T
 *
 * @implements ComparatorInterface<T>
 */
final readonly class ReverseComparator implements ComparatorInterface
{
    /**
     * @param ComparatorInterface<T> $inner
     */
    public function __construct(
        private ComparatorInterface $inner,
    ) {
    }

    public function compare(mixed $a, mixed $b): int
    {
        return -$this->inner->compare($a, $b);
    }
}
