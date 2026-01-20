<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\State;

use HrjSnz\SortedLinkedList\Node;
use HrjSnz\SortedLinkedList\State\ListState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ListState::class)]
#[UsesClass(Node::class)]
final class ListStateTest extends TestCase
{
    #[Test]
    public function initialStateHasNullHeadNullTailAndZeroSize(): void
    {
        /** @var ListState<int> $state */
        $state = new ListState();

        $this->assertNull($state->getHead());
        $this->assertNull($state->getTail());
        $this->assertSame(0, $state->getSize());
        $this->assertTrue($state->isEmpty());
    }

    /**
     * @return array<string, array{0: Node<int>|null, 1: Node<int>|null}>
     */
    public static function provideHeadScenarios(): array
    {
        $node1 = new Node(10);

        return [
            'set and get same node' => [$node1, $node1],
            'set null explicitly' => [null, null],
        ];
    }

    /**
     * @param Node<int>|null $nodeToSet
     * @param Node<int>|null $expectedHead
     */
    #[Test]
    #[DataProvider('provideHeadScenarios')]
    public function setHeadAndGetHeadReturnsCorrectNode(?Node $nodeToSet, ?Node $expectedHead): void
    {
        /** @var ListState<int> $state */
        $state = new ListState();
        $state->setHead($nodeToSet);

        $this->assertSame($expectedHead, $state->getHead());
    }

    #[Test]
    public function setHeadOverridesExistingNode(): void
    {
        /** @var ListState<int> $state */
        $state = new ListState();
        $node1 = new Node(10);
        $node2 = new Node(20);

        $state->setHead($node1);
        $state->setHead($node2);

        $this->assertSame($node2, $state->getHead());
    }

    #[Test]
    public function setNullAfterHavingNodeClearsHead(): void
    {
        /** @var ListState<int> $state */
        $state = new ListState();
        $node1 = new Node(10);

        $state->setHead($node1);
        $state->setHead(null);

        $this->assertNull($state->getHead());
    }

    /**
     * @return array<string, array{0: Node<int>|null, 1: Node<int>|null}>
     */
    public static function provideTailScenarios(): array
    {
        $node1 = new Node(10);

        return [
            'set and get same node' => [$node1, $node1],
            'set null explicitly' => [null, null],
        ];
    }

    /**
     * @param Node<int>|null $nodeToSet
     * @param Node<int>|null $expectedTail
     */
    #[Test]
    #[DataProvider('provideTailScenarios')]
    public function setTailAndGetTailReturnsCorrectNode(?Node $nodeToSet, ?Node $expectedTail): void
    {
        /** @var ListState<int> $state */
        $state = new ListState();
        $state->setTail($nodeToSet);

        $this->assertSame($expectedTail, $state->getTail());
    }

    #[Test]
    public function setTailOverridesExistingNode(): void
    {
        /** @var ListState<int> $state */
        $state = new ListState();
        $node1 = new Node(10);
        $node2 = new Node(20);

        $state->setTail($node1);
        $state->setTail($node2);

        $this->assertSame($node2, $state->getTail());
    }

    #[Test]
    public function setNullAfterHavingTailClearsTail(): void
    {
        /** @var ListState<int> $state */
        $state = new ListState();
        $node1 = new Node(10);

        $state->setTail($node1);
        $state->setTail(null);

        $this->assertNull($state->getTail());
    }

    /**
     * @return array<string, array{0: int, 1: int}>
     */
    public static function provideIncrementScenarios(): array
    {
        return [
            'single increment from zero' => [1, 1],
            'double increment' => [2, 2],
            'multiple increments' => [5, 5],
        ];
    }

    /**
     * @param int $timesToIncrement
     * @param int $expectedSize
     */
    #[Test]
    #[DataProvider('provideIncrementScenarios')]
    public function incrementSizeIncreasesSizeCorrectly(int $timesToIncrement, int $expectedSize): void
    {
        $state = new ListState();

        for ($i = 0; $i < $timesToIncrement; $i++) {
            $state->incrementSize();
        }

        $this->assertSame($expectedSize, $state->getSize());
    }

    /**
     * @return array<string, array{0: int, 1: int, 2: int}>
     */
    public static function provideDecrementScenarios(): array
    {
        return [
            'decrement from one to zero' => [1, 1, 0],
            'decrement from larger value' => [5, 1, 4],
            'decrement at zero stays zero' => [0, 1, 0], // Edge case: guard clause prevents negative
            'multiple decrements to zero' => [3, 3, 0],
        ];
    }

    /**
     * @param int $initialSize
     * @param int $timesToDecrement
     * @param int $expectedSize
     */
    #[Test]
    #[DataProvider('provideDecrementScenarios')]
    public function decrementSizeDecreasesSizeCorrectly(int $initialSize, int $timesToDecrement, int $expectedSize): void
    {
        $state = new ListState();

        // Set up initial size
        for ($i = 0; $i < $initialSize; $i++) {
            $state->incrementSize();
        }

        // Perform decrements
        for ($i = 0; $i < $timesToDecrement; $i++) {
            $state->decrementSize();
        }

        $this->assertSame($expectedSize, $state->getSize());
    }

    /**
     * @return array<string, array{0: int, 1: bool}>
     */
    public static function provideEmptyStateScenarios(): array
    {
        return [
            'initial state is empty' => [0, true],
            'size greater than zero is not empty' => [1, false],
            'size five is not empty' => [5, false],
        ];
    }

    /**
     * @param int $finalSize
     * @param bool $expectedEmpty
     */
    #[Test]
    #[DataProvider('provideEmptyStateScenarios')]
    public function isEmptyReturnsCorrectState(int $finalSize, bool $expectedEmpty): void
    {
        $state = new ListState();

        for ($i = 0; $i < $finalSize; $i++) {
            $state->incrementSize();
        }

        $this->assertSame($expectedEmpty, $state->isEmpty());
    }

    /**
     * @return array<string, array{0: callable, 1: bool}>
     */
    public static function provideConsistencyScenarios(): array
    {
        $node1 = new Node(10);
        $node2 = new Node(20);

        return [
            'add node increases size and not empty' => [
                /** @var callable(ListState<int>): void */
                function (ListState $s) use ($node1): void {
                    $s->setHead($node1);
                    $s->incrementSize();
                },
                false,
            ],
            'remove last node results in empty' => [
                /** @var callable(ListState<int>): void */
                function (ListState $s): void {
                    $s->incrementSize();
                    $s->decrementSize();
                },
                true,
            ],
            'replace head does not affect size' => [
                /** @var callable(ListState<int>): void */
                function (ListState $s) use ($node1, $node2): void {
                    $s->setHead($node1);
                    $s->setHead($node2);
                },
                true, // still empty because size is 0
            ],
            'increment then decrement multiple times' => [
                /** @var callable(ListState<int>): void */
                function (ListState $s): void {
                    $s->incrementSize();
                    $s->incrementSize();
                    $s->incrementSize();
                    $s->decrementSize();
                    $s->decrementSize();
                },
                false, // size is 1, not empty
            ],
        ];
    }

    /**
     * @param callable(ListState<int>): void $operations
     * @param bool $expectedEmpty
     */
    #[Test]
    #[DataProvider('provideConsistencyScenarios')]
    public function stateConsistencyAfterMultipleOperations(callable $operations, bool $expectedEmpty): void
    {
        /** @var ListState<int> $state */
        $state = new ListState();

        $operations($state);

        $this->assertSame($expectedEmpty, $state->isEmpty());
    }
}
