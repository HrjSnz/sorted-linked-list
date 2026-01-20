<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Comparator;

use InvalidArgumentException;

/**
 * @implements ComparatorInterface<int>
 */
final readonly class IntComparator implements ComparatorInterface
{
    public function compare(mixed $a, mixed $b): int
    {
        // @phpstan-ignore-next-line
        if (!is_int($a) || !is_int($b)) {

            throw new InvalidArgumentException(
                sprintf(
                    'Compare only integers. Got %s, %s',
                    var_export($a, true),
                    var_export($b, true),
                )
            );
        }

        return $a <=> $b;
    }
}
