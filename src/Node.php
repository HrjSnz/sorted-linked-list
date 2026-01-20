<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList;

/**
 * @template T
 */
final class Node
{
    /**
     * @param T $value
     * @param Node<T>|null $next
     */
    public function __construct(
        private readonly mixed $value,
        private ?Node $next = null,
    ) {
    }

    /**
     * @return T
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return Node<T>|null
     */
    public function getNext(): ?Node
    {
        return $this->next;
    }

    /**
     * @param Node<T>|null $next
     */
    public function setNext(?Node $next): void
    {
        $this->next = $next;
    }
}
