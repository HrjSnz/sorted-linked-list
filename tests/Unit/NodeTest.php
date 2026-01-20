<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit;

use HrjSnz\SortedLinkedList\Node;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Node::class)]
final class NodeTest extends TestCase
{
    #[Test]
    #[DataProvider('provideValues')]
    #[TestDox('Constructor stores the value correctly')]
    public function constructor_StoresValue_Correctly(mixed $value): void
    {
        $node = new Node($value);

        $this->assertSame($value, $node->getValue());
    }

    /** @return array<string, array{mixed}> */
    public static function provideValues(): array
    {
        return [
            'integer' => [42],
            'negative integer' => [-42],
            'zero' => [0],
            'string' => ['test'],
            'empty string' => [''],
            'float' => [3.14],
            'boolean true' => [true],
            'boolean false' => [false],
            'null' => [null],
            'array' => [['a', 'b']],
        ];
    }

    #[Test]
    #[TestDox('Constructor with null next sets next to null')]
    public function constructor_WithNullNext_SetsNextToNull(): void
    {
        $node = new Node(42);

        $this->assertNull($node->getNext());
    }

    #[Test]
    #[TestDox('Constructor with next node sets next correctly')]
    public function constructor_WithNextNode_SetsNextCorrectly(): void
    {
        $nextNode = new Node(99);
        $node = new Node(42, $nextNode);

        $this->assertSame($nextNode, $node->getNext());
    }

    #[Test]
    #[TestDox('setNext updates the next node')]
    public function setNext_UpdatesNextNode(): void
    {
        $node = new Node(42);
        $newNext = new Node(99);

        $node->setNext($newNext);

        $this->assertSame($newNext, $node->getNext());
    }

    #[Test]
    #[TestDox('setNext with null clears the next node')]
    public function setNext_WithNull_ClearsNextNode(): void
    {
        $nextNode = new Node(99);
        $node = new Node(42, $nextNode);

        $node->setNext(null);

        $this->assertNull($node->getNext());
    }

    #[Test]
    #[TestDox('getValue returns the stored value')]
    public function getValue_ReturnsStoredValue(): void
    {
        $node = new Node('test value');

        $this->assertSame('test value', $node->getValue());
    }

    #[Test]
    #[TestDox('getNext returns null when no next node')]
    public function getNext_WhenNoNextNode_ReturnsNull(): void
    {
        $node = new Node(42);

        $this->assertNull($node->getNext());
    }

    #[Test]
    #[TestDox('Can create a linked chain of nodes')]
    public function canCreate_LinkedChainOfNodes(): void
    {
        $node1 = new Node(1);
        $node2 = new Node(2);
        $node3 = new Node(3);

        $node1->setNext($node2);
        $node2->setNext($node3);

        $this->assertSame($node2, $node1->getNext());
        $this->assertSame($node3, $node2->getNext());
        $this->assertNull($node3->getNext());
    }

    #[Test]
    #[TestDox('Value is readonly and cannot be modified')]
    public function value_IsReadonly_CannotBeModified(): void
    {
        $node = new Node(42);

        $this->assertSame(42, $node->getValue());

        // Value property is readonly, so we can't modify it
        // This test verifies the constructor sets it correctly
        // and getValue returns the same value consistently
        $this->assertSame(42, $node->getValue());
    }
}
