<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Operation;

use HrjSnz\SortedLinkedList\Comparator\ComparatorInterface;
use HrjSnz\SortedLinkedList\Node;
use HrjSnz\SortedLinkedList\State\ListState;

/**
 * @template T
 */
final readonly class NodeRemover
{
    /**
     * @param ListState<T> $state
     */
    public function __construct(
        private ListState $state,
    ) {
    }

    /**
     * @return Node<T>|null
     */
    public function removeHead(): ?Node
    {
        $head = $this->state->getHead();

        if ($head === null) {
            return null;
        }

        $isDeletingTail = ($head === $this->state->getTail());
        $this->state->setHead($head->getNext());
        $head->setNext(null);
        $this->state->decrementSize();

        if ($isDeletingTail) {
            $this->state->setTail($this->state->getHead());
        }

        return $head;
    }

    /**
     * @param Node<T> $previous
     * @return Node<T>|null
     */
    public function removeAfter(Node $previous): ?Node
    {
        $toRemove = $previous->getNext();

        if ($toRemove === null) {
            return null;
        }

        $isDeletingTail = ($toRemove === $this->state->getTail());
        $previous->setNext($toRemove->getNext());
        $toRemove->setNext(null);
        $this->state->decrementSize();

        if ($isDeletingTail) {
            $this->state->setTail($previous);
        }

        return $toRemove;
    }

    /**
     * @param T $value
     * @param ComparatorInterface<T> $comparator
     * @return int<0, max>
     */
    public function removeAllMatching(mixed $value, ComparatorInterface $comparator): int
    {
        $deletedCount = 0;
        $previous = null;
        $current = $this->state->getHead();

        while ($current !== null) {
            if ($comparator->compare($value, $current->getValue()) === 0) {
                if ($previous === null) {
                    $this->removeHead();
                    $current = $this->state->getHead();
                } else {
                    $this->removeAfter($previous);
                    $current = $previous->getNext();
                }
                $deletedCount++;
            } else {
                $previous = $current;
                $current = $current->getNext();
            }
        }

        return $deletedCount;
    }
}
