<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Comparator;

/**
 * @template T
 */
interface ComparatorInterface
{
    /**
     * @param T $a
     * @param T $b
     */
    public function compare(mixed $a, mixed $b): int;
}
