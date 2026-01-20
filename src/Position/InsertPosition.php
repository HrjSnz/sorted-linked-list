<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Position;

use HrjSnz\SortedLinkedList\Node;

/**
 * @template T
 */
final readonly class InsertPosition
{
    /**
     * @param Node<T>|null $node
     */
    private function __construct(
        public ?Node $node,
        public bool  $isDuplicate = false,
    ) {
    }

    /**
     * @return InsertPosition<T>
     */
    public static function head(): InsertPosition
    {
        /** @phpstan-ignore-next-line */
        return new self(null);
    }

    /**
     * @param Node<T> $node
     * @return InsertPosition<T>
     */
    public static function after(Node $node): InsertPosition
    {
        return new self($node);
    }

    /**
     * @return InsertPosition<T>
     */
    public static function none(): InsertPosition
    {
        /** @phpstan-ignore-next-line */
        return new self(null, isDuplicate: true);
    }
}
