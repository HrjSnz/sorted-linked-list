<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Position;

use HrjSnz\SortedLinkedList\Node;

/**
 * @template T
 */
final readonly class DeletePosition
{
    /**
     * @param Node<T>|null $foundNode
     * @param Node<T>|null $previousNode
     */
    private function __construct(
        public ?Node $foundNode,
        public ?Node $previousNode,
    ) {
    }

    /**
     * @return DeletePosition<T>
     */
    public static function notFound(): self
    {
        /** @var DeletePosition<T> */
        return new self(null, null);
    }

    /**
     * @param Node<T> $node
     * @return DeletePosition<T>
     */
    public static function foundAtHead(Node $node): self
    {
        return new self($node, null);
    }

    /**
     * @param Node<T> $node
     * @param Node<T> $previous
     * @return DeletePosition<T>
     */
    public static function foundAfter(Node $node, Node $previous): self
    {
        return new self($node, $previous);
    }
}
