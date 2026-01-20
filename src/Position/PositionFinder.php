<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Position;

use HrjSnz\SortedLinkedList\Comparator\ComparatorInterface;
use HrjSnz\SortedLinkedList\Node;

/**
 * @template T
 */
final readonly class PositionFinder
{
    /**
     * @param ComparatorInterface<T> $comparator
     */
    public function __construct(
        private ComparatorInterface $comparator,
        private bool $allowDuplicates = true,
    ) {
    }

    /**
     * @param T $value
     * @param Node<T>|null $head
     * @return InsertPosition<T>
     */
    public function findInsertPosition(mixed $value, ?Node $head): InsertPosition
    {
        if ($head === null) {
            /** @phpstan-ignore-next-line */
            return InsertPosition::head();
        }

        $headComparison = $this->comparator->compare($value, $head->getValue());

        if (!$this->allowDuplicates && $headComparison === 0) {
            /** @phpstan-ignore-next-line */
            return InsertPosition::none();
        }

        if ($headComparison < 0) {
            /** @phpstan-ignore-next-line */
            return InsertPosition::head();
        }

        $current = $head;

        while ($current->getNext() !== null) {
            $nextComparison = $this->comparator->compare($value, $current->getNext()->getValue());

            if (!$this->allowDuplicates && $nextComparison === 0) {
                /** @phpstan-ignore-next-line */
                return InsertPosition::none();
            }

            if ($nextComparison < 0) {
                /** @phpstan-ignore-next-line */
                return InsertPosition::after($current);
            }

            $current = $current->getNext();
        }

        /** @phpstan-ignore-next-line */
        return InsertPosition::after($current);
    }

    /**
     * @param T $value
     * @param Node<T>|null $head
     * @return Node<T>|null
     */
    public function findNode(mixed $value, ?Node $head): ?Node
    {
        $current = $head;

        while ($current !== null) {
            if ($this->comparator->compare($value, $current->getValue()) === 0) {
                return $current;
            }
            $current = $current->getNext();
        }

        return null;
    }

    /**
     * @param Node<T> $target
     * @param Node<T>|null $head
     * @return Node<T>|null
     */
    public function findPreviousNode(Node $target, ?Node $head): ?Node
    {
        if ($head === null || $head === $target) {
            return null;
        }

        $current = $head;

        while ($current->getNext() !== null) {
            if ($current->getNext() === $target) {
                return $current;
            }
            $current = $current->getNext();
        }

        return null;
    }

    /**
     * @param T $value
     * @param Node<T>|null $head
     * @return DeletePosition<T>
     */
    public function findDeletePosition(mixed $value, ?Node $head): DeletePosition
    {
        if ($head === null) {
            /** @phpstan-ignore-next-line */
            return DeletePosition::notFound();
        }

        if ($this->comparator->compare($value, $head->getValue()) === 0) {
            /** @phpstan-ignore-next-line */
            return DeletePosition::foundAtHead($head);
        }

        $previous = $head;
        $current = $head->getNext();

        while ($current !== null) {
            if ($this->comparator->compare($value, $current->getValue()) === 0) {
                /** @phpstan-ignore-next-line */
                return DeletePosition::foundAfter($current, $previous);
            }
            $previous = $current;
            $current = $current->getNext();
        }

        /** @phpstan-ignore-next-line */
        return DeletePosition::notFound();
    }
}
