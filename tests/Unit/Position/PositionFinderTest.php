<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\Position;

use HrjSnz\SortedLinkedList\Comparator\IntComparator;
use HrjSnz\SortedLinkedList\Node;
use HrjSnz\SortedLinkedList\Position\DeletePosition;
use HrjSnz\SortedLinkedList\Position\InsertPosition;
use HrjSnz\SortedLinkedList\Position\PositionFinder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PositionFinder::class)]
#[UsesClass(Node::class)]
#[UsesClass(IntComparator::class)]
#[UsesClass(DeletePosition::class)]
final class PositionFinderTest extends TestCase
{
    /** @var PositionFinder<int> */
    private PositionFinder $finder;

    protected function setUp(): void
    {
        $this->finder = new PositionFinder(new IntComparator());
    }

    /**
     * @return array<string, array{0: int, 1: Node<int>|null, 2: (Node<int>|null), 3: bool}>
     */
    public static function provideInsertPositions(): array
    {
        $cases = [];

        $cases['empty list'] = [42, null, null, true];

        $cases['insert at beginning'] = [
            5,
            new Node(10),
            null,
            true,
        ];

        $middleList = new Node(10, new Node(20, new Node(30)));
        $cases['insert in middle'] = [
            25,
            $middleList,
            $middleList->getNext(),
            true,
        ];

        $node1 = new Node(10);
        $node2 = new Node(20);
        $node1->setNext($node2);
        $cases['insert at end'] = [
            30,
            $node1,
            $node2,
            true,
        ];

        $dupList = new Node(10, new Node(20, new Node(30)));
        $cases['duplicate value'] = [
            20,
            $dupList,
            null,  // duplicate returns null node with isDuplicate=true
            false,  // allowDuplicates=false for this case
        ];

        return $cases;
    }

    /**
     * @param int $value
     * @param Node<int>|null $head
     * @param Node<int>|null $expected
     * @param bool $allowDuplicates
     */
    #[Test]
    #[DataProvider('provideInsertPositions')]
    public function findInsertPositionReturnsCorrectNode(
        int $value,
        ?Node $head,
        ?Node $expected,
        bool $allowDuplicates
    ): void {
        $finder = $allowDuplicates
            ? $this->finder
            : new PositionFinder(new IntComparator(), allowDuplicates: false);

        $result = $finder->findInsertPosition($value, $head);

        $this->assertInstanceOf(InsertPosition::class, $result);
        $this->assertSame($expected, $result->node);
    }

    /**
     * @return array<string, array{0: int, 1: Node<int>|null, 2: Node<int>|null}>
     */
    public static function provideNodesForFinding(): array
    {
        $cases = [];

        $cases['empty list'] = [42, null, null];

        $head = new Node(10, new Node(20, new Node(30)));
        $cases['node at head'] = [10, $head, $head];

        $list = new Node(10, new Node(20, new Node(30)));
        $cases['node in middle'] = [20, $list, $list->getNext()];

        $tailList = new Node(10, new Node(20, new Node(30)));
        /** @var Node<int> $middleNode */
        $middleNode = $tailList->getNext();
        /** @var Node<int> $tailNode */
        $tailNode = $middleNode->getNext();
        $cases['node at tail'] = [30, $tailList, $tailNode];

        $cases['node not found'] = [99, new Node(10, new Node(20)), null];

        return $cases;
    }

    /**
     * @param int $value
     * @param Node<int>|null $head
     * @param Node<int>|null $expected
     */
    #[Test]
    #[DataProvider('provideNodesForFinding')]
    public function findNodeReturnsCorrectNode(int $value, ?Node $head, ?Node $expected): void
    {
        $result = $this->finder->findNode($value, $head);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{0: Node<int>, 1: Node<int>|null, 2: Node<int>|null}>
     */
    public static function providePreviousNodes(): array
    {
        $cases = [];

        $cases['empty list'] = [new Node(42), null, null];

        $head = new Node(10, new Node(20, new Node(30)));
        $cases['target is head'] = [$head, $head, null];

        $middleList = new Node(10, new Node(20, new Node(30)));
        /** @var Node<int> $middleTarget */
        $middleTarget = $middleList->getNext();
        $cases['target in middle'] = [$middleTarget, $middleList, $middleList];

        $tailList = new Node(10, new Node(20, new Node(30)));
        /** @var Node<int> $middleNodeForTail */
        $middleNodeForTail = $tailList->getNext();
        /** @var Node<int> $tailTarget */
        $tailTarget = $middleNodeForTail->getNext();
        $cases['target is tail'] = [$tailTarget, $tailList, $middleNodeForTail];

        $target = new Node(99);
        $cases['target not in list'] = [$target, new Node(10, new Node(20)), null];

        return $cases;
    }

    /**
     * @param Node<int> $target
     * @param Node<int>|null $head
     * @param Node<int>|null $expected
     */
    #[Test]
    #[DataProvider('providePreviousNodes')]
    public function findPreviousNodeReturnsCorrectNode(Node $target, ?Node $head, ?Node $expected): void
    {
        $result = $this->finder->findPreviousNode($target, $head);

        $this->assertSame($expected, $result);
    }

    /** @return array<string, array{int, Node<int>|null, DeletePosition}> */
    /** @phpstan-ignore-next-line */
    public static function provideDeletePositions(): array
    {
        $cases = [];

        $cases['empty list'] = [42, null, DeletePosition::notFound()];

        $head = new Node(10, new Node(20, new Node(30)));
        /** @phpstan-ignore-next-line */
        $cases['value at head'] = [10, $head, DeletePosition::foundAtHead($head)];

        $middleList = new Node(10, new Node(20, new Node(30)));
        /** @phpstan-ignore-next-line */
        $cases['value in middle'] = [20, $middleList, DeletePosition::foundAfter($middleList->getNext(), $middleList)];

        $tailList = new Node(10, new Node(20, new Node(30)));
        $middle = $tailList->getNext();
        /** @phpstan-ignore-next-line */
        $cases['value at tail'] = [30, $tailList, DeletePosition::foundAfter($middle->getNext(), $middle)];

        $cases['value not found'] = [99, new Node(10, new Node(20)), DeletePosition::notFound()];

        return $cases;
    }

    /**
     * @param int $value
     * @param Node<int>|null $head
     * @param DeletePosition<int> $expected
     */
    #[Test]
    #[DataProvider('provideDeletePositions')]
    public function findDeletePositionReturnsCorrectPosition(int $value, ?Node $head, DeletePosition $expected): void
    {
        $result = $this->finder->findDeletePosition($value, $head);

        $this->assertSame($expected->foundNode, $result->foundNode);
        $this->assertSame($expected->previousNode, $result->previousNode);
    }
}
