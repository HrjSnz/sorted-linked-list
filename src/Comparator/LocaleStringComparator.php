<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Comparator;

use Collator;
use InvalidArgumentException;

/**
 * @implements ComparatorInterface<string>
 */
final readonly class LocaleStringComparator implements ComparatorInterface
{
    private Collator $collator;

    public function __construct(
        string $locale,
        int $strength = Collator::TERTIARY,
    ) {
        $this->collator = new Collator($locale);
        $this->collator->setStrength($strength);
    }

    public function compare(mixed $a, mixed $b): int
    {
        // @phpstan-ignore-next-line
        if (!is_string($a) || !is_string($b)) {
            throw new InvalidArgumentException('Compare only strings');
        }

        $result = $this->collator->compare($a, $b);

        if ($result === false) {
            throw new InvalidArgumentException('Collator comparison failed');
        }

        return $result;
    }
}
