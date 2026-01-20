<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\Operation;

use HrjSnz\SortedLinkedList\Comparator\IntComparator;
use HrjSnz\SortedLinkedList\Node;
use HrjSnz\SortedLinkedList\Operation\NodeRemover;
use HrjSnz\SortedLinkedList\State\ListState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NodeRemover::class)]
#[UsesClass(ListState::class)]
#[UsesClass(Node::class)]
#[UsesClass(IntComparator::class)]
final class NodeRemoverTest extends TestCase
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
    public function removeHeadWhenListIsEmptyReturnsNull(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);

        $removed = $remover->removeHead();

        $this->assertNull($removed);
        $this->assertNull($state->getHead());
        $this->assertNull($state->getTail());
        $this->assertSame(0, $state->getSize());
    }

    #[Test]
    public function removeHeadWhenListHasOneElement(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $node = $this->createNode(42);
        $state->setHead($node);
        $state->setTail($node);
        $state->incrementSize();

        $removed = $remover->removeHead();

        $this->assertSame($node, $removed);
        $this->assertNull($state->getHead());
        $this->assertNull($state->getTail());
        $this->assertSame(0, $state->getSize());
    }

    #[Test]
    public function removeHeadWhenListHasMultipleElements(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $tail = $this->createNode(20);
        $head = $this->createNode(10, $tail);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeHead();

        $this->assertSame($head, $removed);
        $this->assertSame($tail, $state->getHead());
        $this->assertSame($tail, $state->getTail());
        $this->assertSame(1, $state->getSize());
    }

    #[Test]
    public function removeHeadUpdatesSize(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $tail = $this->createNode(20);
        $head = $this->createNode(10, $tail);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();

        $remover->removeHead();

        $this->assertSame(1, $state->getSize());
    }

    #[Test]
    public function removeHeadSeversOldHeadLinks(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $tail = $this->createNode(20);
        $head = $this->createNode(10, $tail);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeHead();

        $this->assertNotNull($removed);
        $this->assertNull($removed->getNext());
    }

    #[Test]
    public function removeAfterWhenTargetHasNoNextReturnsNull(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $tail = $this->createNode(20);
        $state->setHead($tail);
        $state->setTail($tail);
        $state->incrementSize();

        $removed = $remover->removeAfter($tail);

        $this->assertNull($removed);
        $this->assertSame(1, $state->getSize());
    }

    #[Test]
    public function removeAfterWhenRemovingMiddleNode(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $tail = $this->createNode(30);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAfter($head);

        $this->assertSame($middle, $removed);
        $this->assertSame($tail, $head->getNext());
        $this->assertSame(2, $state->getSize());
    }

    #[Test]
    public function removeAfterWhenRemovingTail(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $tail = $this->createNode(30);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAfter($middle);

        $this->assertSame($tail, $removed);
        $this->assertSame($middle, $state->getTail());
        $this->assertNull($middle->getNext());
        $this->assertSame(2, $state->getSize());
    }

    #[Test]
    public function removeAfterWhenOnlyTwoElements(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $tail = $this->createNode(20);
        $head = $this->createNode(10, $tail);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAfter($head);

        $this->assertSame($tail, $removed);
        $this->assertSame($head, $state->getHead());
        $this->assertSame($head, $state->getTail());
        $this->assertSame(1, $state->getSize());
    }

    #[Test]
    public function removeAfterUpdatesSize(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $tail = $this->createNode(30);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $remover->removeAfter($head);

        $this->assertSame(2, $state->getSize());
    }

    #[Test]
    public function removeAfterMaintainsRemainingLinks(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $tail = $this->createNode(30);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $remover->removeAfter($head);

        $this->assertSame($tail, $head->getNext());
    }

    #[Test]
    public function removeAfterSeversRemovedNodeLinks(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $tail = $this->createNode(30);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAfter($head);

        $this->assertNotNull($removed);
        $this->assertNull($removed->getNext());
    }

    #[Test]
    public function removeAllMatchingWhenListIsEmptyReturnsZero(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();

        $removed = $remover->removeAllMatching(10, $comparator);

        $this->assertSame(0, $removed);
        $this->assertNull($state->getHead());
        $this->assertNull($state->getTail());
        $this->assertSame(0, $state->getSize());
    }

    #[Test]
    public function removeAllMatchingWhenNoMatchesFoundReturnsZero(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();
        $tail = $this->createNode(30);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAllMatching(99, $comparator);

        $this->assertSame(0, $removed);
        $this->assertSame($head, $state->getHead());
        $this->assertSame($tail, $state->getTail());
        $this->assertSame(3, $state->getSize());
    }

    #[Test]
    public function removeAllMatchingWhenSingleMatchAtHead(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();
        $tail = $this->createNode(30);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAllMatching(10, $comparator);

        $this->assertSame(1, $removed);
        $this->assertSame($middle, $state->getHead());
        $this->assertSame($tail, $state->getTail());
        $this->assertSame(2, $state->getSize());
    }

    #[Test]
    public function removeAllMatchingWhenSingleMatchInMiddle(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();
        $tail = $this->createNode(30);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAllMatching(20, $comparator);

        $this->assertSame(1, $removed);
        $this->assertSame($head, $state->getHead());
        $this->assertSame($tail, $state->getTail());
        $this->assertSame($tail, $head->getNext());
        $this->assertSame(2, $state->getSize());
    }

    #[Test]
    public function removeAllMatchingWhenSingleMatchAtTail(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();
        $tail = $this->createNode(30);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAllMatching(30, $comparator);

        $this->assertSame(1, $removed);
        $this->assertSame($head, $state->getHead());
        $this->assertSame($middle, $state->getTail());
        $this->assertNull($middle->getNext());
        $this->assertSame(2, $state->getSize());
    }

    #[Test]
    public function removeAllMatchingWhenMultipleConsecutiveMatches(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();
        $tail = $this->createNode(30);
        $node3 = $this->createNode(20, $tail);
        $node2 = $this->createNode(20, $node3);
        $head = $this->createNode(10, $node2);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAllMatching(20, $comparator);

        $this->assertSame(2, $removed);
        $this->assertSame($head, $state->getHead());
        $this->assertSame($tail, $state->getTail());
        $this->assertSame($tail, $head->getNext());
        $this->assertSame(2, $state->getSize());
    }

    #[Test]
    public function removeAllMatchingWhenMultipleNonConsecutiveMatches(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();
        $tail = $this->createNode(20);
        $node3 = $this->createNode(30, $tail);
        $node2 = $this->createNode(20, $node3);
        $head = $this->createNode(10, $node2);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAllMatching(20, $comparator);

        $this->assertSame(2, $removed);
        $this->assertSame($head, $state->getHead());
        $this->assertSame($node3, $state->getTail());
        $this->assertSame($node3, $head->getNext());
        $this->assertNull($node3->getNext());
        $this->assertSame(2, $state->getSize());
    }

    #[Test]
    public function removeAllMatchingWhenAllElementsMatch(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();
        $tail = $this->createNode(10);
        $middle = $this->createNode(10, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAllMatching(10, $comparator);

        $this->assertSame(3, $removed);
        $this->assertNull($state->getHead());
        $this->assertNull($state->getTail());
        $this->assertSame(0, $state->getSize());
    }

    #[Test]
    public function removeAllMatchingWhenFirstElementDoesNotMatchAndRestDo(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();
        $tail = $this->createNode(20);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAllMatching(20, $comparator);

        $this->assertSame(2, $removed);
        $this->assertSame($head, $state->getHead());
        $this->assertSame($head, $state->getTail());
        $this->assertNull($head->getNext());
        $this->assertSame(1, $state->getSize());
    }

    #[Test]
    public function removeAllMatchingWhenLastElementDoesNotMatchAndOthersDo(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();
        $tail = $this->createNode(30);
        $middle = $this->createNode(20, $tail);
        $head = $this->createNode(20, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removed = $remover->removeAllMatching(20, $comparator);

        $this->assertSame(2, $removed);
        $this->assertSame($tail, $state->getHead());
        $this->assertSame($tail, $state->getTail());
        $this->assertNull($tail->getNext());
        $this->assertSame(1, $state->getSize());
    }

    #[Test]
    public function removeAllMatchingWhenSingleElementMatches(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();
        $node = $this->createNode(10);
        $state->setHead($node);
        $state->setTail($node);
        $state->incrementSize();

        $removed = $remover->removeAllMatching(10, $comparator);

        $this->assertSame(1, $removed);
        $this->assertNull($state->getHead());
        $this->assertNull($state->getTail());
        $this->assertSame(0, $state->getSize());
    }

    #[Test]
    public function removeAllMatchingProperlyUpdatesSize(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();
        $tail = $this->createNode(20);
        $middle = $this->createNode(10, $tail);
        $head = $this->createNode(10, $middle);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $removedCount = $remover->removeAllMatching(10, $comparator);

        $this->assertSame(2, $removedCount);
        $this->assertSame(1, $state->getSize());
    }

    #[Test]
    public function removeAllMatchingMaintainsListIntegrity(): void
    {
        $state = new ListState();
        $remover = new NodeRemover($state);
        $comparator = new IntComparator();
        $tail = $this->createNode(40);
        $node4 = $this->createNode(30, $tail);
        $node3 = $this->createNode(20, $node4);
        $node2 = $this->createNode(10, $node3);
        $head = $this->createNode(10, $node2);
        $state->setHead($head);
        $state->setTail($tail);
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();
        $state->incrementSize();

        $remover->removeAllMatching(10, $comparator);

        // Verify remaining list is intact: 20 -> 30 -> 40
        $this->assertSame($node3, $state->getHead());
        $this->assertSame($tail, $state->getTail());
        $this->assertSame($node4, $node3->getNext());
        $this->assertSame($tail, $node4->getNext());
        $this->assertNull($tail->getNext());
        $this->assertSame(3, $state->getSize());
    }
}
