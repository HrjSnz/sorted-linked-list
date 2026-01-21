<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList;

use Countable;
use IteratorAggregate;
use InvalidArgumentException;
use HrjSnz\SortedLinkedList\Comparator\ComparatorInterface;
use HrjSnz\SortedLinkedList\Operation\NodeInserter;
use HrjSnz\SortedLinkedList\Operation\NodeRemover;
use HrjSnz\SortedLinkedList\Position\PositionFinder;
use HrjSnz\SortedLinkedList\State\ListState;
use Traversable;

/**
 * @template T
 * @implements IteratorAggregate<int, T>
 */
abstract class SortedLinkedListEngine implements Countable, IteratorAggregate
{
    /** @var ListState<T> */
    private ListState $state;

    /** @var PositionFinder<T> */
    private PositionFinder $positionFinder;

    /** @var NodeInserter<T> */
    private NodeInserter $inserter;

    /** @var NodeRemover<T> */
    private NodeRemover $remover;

    /**
     * @param ComparatorInterface<T> $comparator
     */
    public function __construct(
        private ComparatorInterface $comparator,
        private readonly bool       $allowDuplicates = true,
    ) {
        $this->initializeDependents($comparator);
    }

    /**
     * @param ComparatorInterface<T> $comparator
     */
    private function initializeDependents(ComparatorInterface $comparator): void
    {
        $this->state = new ListState();
        $this->positionFinder = new PositionFinder($comparator, $this->allowDuplicates);
        $this->inserter = new NodeInserter($this->state);
        $this->remover = new NodeRemover($this->state);
    }

    /**
     * @return ComparatorInterface<T>
     */
    public function getComparator(): ComparatorInterface
    {
        return $this->comparator;
    }

    /**
     * @param ComparatorInterface<T> $comparator
     */
    public function setComparator(ComparatorInterface $comparator): void
    {
        $values = iterator_to_array($this->getIterator());

        $this->comparator = $comparator;
        $this->initializeDependents($comparator);

        foreach ($values as $value) {
            $this->insert($value);
        }
    }

    /**
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        $current = $this->state->getHead();

        while ($current !== null) {
            yield $current->getValue();
            $current = $current->getNext();
        }
    }

    /**
     * @return array<int, T>
     */
    public function getDuplicates(): array
    {
        $duplicates = [];
        $counts = $this->getDuplicatesWithCount();

        foreach ($counts as $item) {
            if ($item['count'] > 1) {
                $duplicates[] = $item['value'];
            }
        }

        return $duplicates;
    }

    /**
     * @return array<int, array{value: T, count: int}>
     */
    public function getDuplicatesWithCount(): array
    {
        $duplicates = [];
        $previousValue = null;
        $previousCount = 0;

        foreach ($this->getIterator() as $value) {
            if ($previousValue !== null && $this->comparator->compare($value, $previousValue) === 0) {
                $previousCount++;
            } else {
                if ($previousCount > 1) {
                    /** @var T $previousValue */
                    $duplicates[] = ['value' => $previousValue, 'count' => $previousCount];
                }
                $previousValue = $value;
                $previousCount = 1;
            }
        }

        if ($previousCount > 1) {
            /** @var T $previousValue */
            $duplicates[] = ['value' => $previousValue, 'count' => $previousCount];
        }

        return $duplicates;
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return $this->state->getSize();
    }

    public function isEmpty(): bool
    {
        return $this->state->isEmpty();
    }

    /**
     * @return T|null
     */
    public function first(): mixed
    {
        return $this->state->getHead()?->getValue();
    }

    /**
     * @return T|null
     */
    public function last(): mixed
    {
        return $this->state->getTail()?->getValue();
    }

    /**
     * @param iterable<T> $values
     */
    final protected function addAll(iterable $values): void
    {
        $valuesArray = $values instanceof Traversable ? iterator_to_array($values) : $values;

        foreach ($valuesArray as $value) {
            $this->insert($value);
        }
    }

    /**
     * @param T $value
     */
    final protected function insert(mixed $value): void
    {
        $result = $this->positionFinder->findInsertPosition($value, $this->state->getHead());

        if ($result->isDuplicate) {
            return;
        }

        $result->node === null ? $this->inserter->insertHead($value) :
            $this->inserter->insertAfter($result->node, $value);
    }

    /**
     * @param T $value
     */
    final protected function delete(mixed $value): bool
    {
        $position = $this->positionFinder->findDeletePosition($value, $this->state->getHead());

        if ($position->foundNode === null) {
            return false;
        }

        return $position->previousNode === null
            ? $this->remover->removeHead() !== null
            : $this->remover->removeAfter($position->previousNode) !== null;
    }

    /**
     * @param T $value
     * @return int<0, max>
     */
    final protected function deleteAll(mixed $value): int
    {
        return $this->remover->removeAllMatching($value, $this->comparator);
    }

    /**
     * @param T $min
     * @return Traversable<int, T>
     */
    final protected function greaterThan(
        mixed           $min,
        BetweenBoundary $boundary = BetweenBoundary::INCLUSIVE,
    ): Traversable {
        $yielding = false;
        foreach ($this->getIterator() as $value) {
            if ($yielding) {
                yield $value;
            } elseif ($this->isWithinMinBoundary($value, $min, $boundary)) {
                yield $value;
                $yielding = true;
            }
        }
    }

    /**
     * @param T $max
     * @return Traversable<int, T>
     */
    final protected function lessThan(
        mixed           $max,
        BetweenBoundary $boundary = BetweenBoundary::INCLUSIVE,
    ): Traversable {
        foreach ($this->getIterator() as $value) {
            if ($this->isWithinMaxBoundary($value, $max, $boundary)) {
                yield $value;
            } else {
                break;
            }
        }
    }

    /**
     * Finds values within a specified range.
     *
     * @param T $min
     * @param T $max
     * @return Traversable<int, T>
     */
    final protected function inRange(
        mixed           $min,
        mixed           $max,
        BetweenBoundary $minBoundary = BetweenBoundary::INCLUSIVE,
        BetweenBoundary $maxBoundary = BetweenBoundary::INCLUSIVE,
    ): Traversable {
        if ($this->comparator->compare($min, $max) > 0) {
            throw new InvalidArgumentException(
                'Invalid range: lower bound must come before upper bound'
            );
        }

        foreach ($this->getIterator() as $value) {
            if (!$this->isWithinMinBoundary($value, $min, $minBoundary)) {
                continue;
            }

            if (!$this->isWithinMaxBoundary($value, $max, $maxBoundary)) {
                break;
            }

            yield $value;
        }
    }

    /**
     * @param T $value
     * @param T|null $min
     */
    private function isWithinMinBoundary(mixed $value, mixed $min, BetweenBoundary $boundary): bool
    {
        if ($min === null) {
            return true;
        }

        $comparison = $this->comparator->compare($value, $min);

        return $boundary === BetweenBoundary::INCLUSIVE ? $comparison >= 0 : $comparison > 0;
    }

    /**
     * @param T $value
     * @param T|null $max
     */
    private function isWithinMaxBoundary(mixed $value, mixed $max, BetweenBoundary $boundary): bool
    {
        if ($max === null) {
            return true;
        }

        $comparison = $this->comparator->compare($value, $max);

        return $boundary === BetweenBoundary::INCLUSIVE ? $comparison <= 0 : $comparison < 0;
    }

    final protected function has(mixed $value): bool
    {
        return $this->positionFinder->findNode($value, $this->state->getHead()) !== null;
    }
}
