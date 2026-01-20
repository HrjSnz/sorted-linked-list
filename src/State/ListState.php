<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\State;

use HrjSnz\SortedLinkedList\Node;

/**
 * @template T
 */
final class ListState
{
    /** @var Node<T>|null */
    private ?Node $head = null;

    /** @var Node<T>|null */
    private ?Node $tail = null;

    /** @var int<0, max> */
    private int $size = 0;

    /**
     * @param Node<T>|null $node
     */
    public function setHead(?Node $node): void
    {
        $this->head = $node;
    }

    /**
     * @return Node<T>|null
     */
    public function getHead(): ?Node
    {
        return $this->head;
    }

    /**
     * @param Node<T>|null $node
     */
    public function setTail(?Node $node): void
    {
        $this->tail = $node;
    }

    /**
     * @return Node<T>|null
     */
    public function getTail(): ?Node
    {
        return $this->tail;
    }

    public function incrementSize(): void
    {
        $this->size++;
    }

    public function decrementSize(): void
    {
        if ($this->size > 0) {
            $this->size--;
        }
    }

    /**
     * @return int<0, max>
     */
    public function getSize(): int
    {
        return $this->size;
    }

    public function isEmpty(): bool
    {
        return $this->size === 0;
    }
}
