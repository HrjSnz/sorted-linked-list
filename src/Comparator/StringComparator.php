<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Comparator;

use InvalidArgumentException;

/**
 * @implements ComparatorInterface<string>
 */
final readonly class StringComparator implements ComparatorInterface
{
    public function __construct(
        private bool $caseSensitive = true,
    ) {
    }

    public function compare(mixed $a, mixed $b): int
    {
        // @phpstan-ignore-next-line
        if (!is_string($a) || !is_string($b)) {
            throw new InvalidArgumentException('Compare only strings');
        }

        return $this->caseSensitive ? strcmp($a, $b) : strcasecmp($a, $b);
    }
}
