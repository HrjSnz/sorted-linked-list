<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Operation;

use HrjSnz\SortedLinkedList\Node;
use HrjSnz\SortedLinkedList\State\ListState;

/**
 * @template T
 */
final readonly class NodeInserter
{
    /**
     * @param ListState<T> $state
     */
    public function __construct(
        private ListState $state,
    ) {
    }

    /**
     * @param T $value
     */
    public function insertHead(mixed $value): void
    {
        $node = new Node($value);
        $node->setNext($this->state->getHead());
        $this->state->setHead($node);

        if ($this->state->getTail() === null) {
            $this->state->setTail($node);
        }

        $this->state->incrementSize();
    }

    /**
     * @param Node<T> $target
     * @param T $value
     */
    public function insertAfter(Node $target, mixed $value): void
    {
        $newNode = new Node($value);
        $newNode->setNext($target->getNext());
        $target->setNext($newNode);

        if ($newNode->getNext() === null) {
            $this->state->setTail($newNode);
        }

        $this->state->incrementSize();
    }

}
