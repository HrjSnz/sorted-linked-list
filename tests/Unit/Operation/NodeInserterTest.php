<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\Operation;

use HrjSnz\SortedLinkedList\Node;
use HrjSnz\SortedLinkedList\Operation\NodeInserter;
use HrjSnz\SortedLinkedList\State\ListState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NodeInserter::class)]
#[UsesClass(ListState::class)]
#[UsesClass(Node::class)]
final class NodeInserterTest extends TestCase
{
    /**
     * @param mixed $value
     * @param Node<mixed>|null $next
     * @return Node<mixed>
     */
    private function createNode(mixed $value, ?Node $next = null): Node
    {
        /** @var Node<mixed> */
        return new Node($value, $next);
    }

    #[Test]
    public function insertAtBeginningWhenListIsEmpty(): void
    {
        $state = new ListState();
        $inserter = new NodeInserter($state);

        $inserter->insertHead(42);

        $head = $state->getHead();
        $this->assertSame(42, $head->getValue());
        $this->assertSame($head, $state->getTail());
        $this->assertSame(1, $state->getSize());
    }

    #[Test]
    public function insertAtBeginningWhenListHasNodes(): void
    {
        $state = new ListState();
        $inserter = new NodeInserter($state);
        $oldHead = $this->createNode(10);
        $state->setHead($oldHead);
        $state->incrementSize();

        $inserter->insertHead(5);

        $newHead = $state->getHead();
        $this->assertSame(5, $newHead->getValue());
        $this->assertSame($oldHead, $newHead->getNext());
        $this->assertSame(2, $state->getSize());
    }

    #[Test]
    public function insertAtBeginningPreservesTailWhenListHasNodes(): void
    {
        $state = new ListState();
        $inserter = new NodeInserter($state);
        $tail = $this->createNode(20);
        $head = $this->createNode(10, $tail);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();

        $inserter->insertHead(5);

        $newHead = $state->getHead();
        $this->assertSame($tail, $state->getTail());
        $this->assertSame(5, $newHead->getValue());
        $this->assertSame($head, $newHead->getNext());
    }

    #[Test]
    public function insertAfterWhenTargetInMiddle(): void
    {
        $state = new ListState();
        $inserter = new NodeInserter($state);
        $tail = $this->createNode(30);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $inserter->insertAfter($middle, 25);

        $this->assertSame(25, $middle->getNext()->getValue());
        $this->assertSame($tail, $middle->getNext()->getNext());
        $this->assertSame(4, $state->getSize());
    }

    #[Test]
    public function insertAfterWhenTargetIsTail(): void
    {
        $state = new ListState();
        $inserter = new NodeInserter($state);
        $tail = $this->createNode(20);
        $head = $this->createNode(10, $tail);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();

        $inserter->insertAfter($tail, 30);

        $this->assertSame(30, $state->getTail()->getValue());
        $this->assertNull($state->getTail()->getNext());
    }

    #[Test]
    public function insertAfterMaintainsForwardLinks(): void
    {
        $state = new ListState();
        $inserter = new NodeInserter($state);
        $tail = $this->createNode(30);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $inserter->insertAfter($middle, 25);

        $newNode = $middle->getNext();
        $this->assertSame($middle, $head->getNext());
        $this->assertSame(25, $newNode->getValue());
        $this->assertSame($tail, $newNode->getNext());
    }

}
