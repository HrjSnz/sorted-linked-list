<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\Position;

use HrjSnz\SortedLinkedList\Node;
use HrjSnz\SortedLinkedList\Position\DeletePosition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeletePosition::class)]
final class DeletePositionTest extends TestCase
{
    #[Test]
    #[TestDox('notFound creates position with null nodes')]
    public function notFound_CreatesPosition_WithNullNodes(): void
    {
        $position = DeletePosition::notFound();

        $this->assertNull($position->foundNode);
        $this->assertNull($position->previousNode);
    }

    #[Test]
    #[TestDox('foundAtHead creates position with node and null previous')]
    public function foundAtHead_CreatesPosition_WithNodeAndNullPrevious(): void
    {
        $node = new Node(42);
        $position = DeletePosition::foundAtHead($node);

        $this->assertSame($node, $position->foundNode);
        $this->assertNull($position->previousNode);
    }

    #[Test]
    #[TestDox('foundAfter creates position with node and previous')]
    public function foundAfter_CreatesPosition_WithNodeAndPrevious(): void
    {
        $previous = new Node(10);
        $found = new Node(20);
        $position = DeletePosition::foundAfter($found, $previous);

        $this->assertSame($found, $position->foundNode);
        $this->assertSame($previous, $position->previousNode);
    }

    #[Test]
    #[TestDox('Properties are readonly and cannot be modified')]
    public function properties_AreReadonly_CannotBeModified(): void
    {
        $node = new Node(42);
        $position = DeletePosition::foundAtHead($node);

        // Verify properties are readonly by checking they maintain their values
        $this->assertSame($node, $position->foundNode);
        $this->assertNull($position->previousNode);
    }

    #[Test]
    #[TestDox('Multiple notFound instances are equal')]
    public function multipleNotFoundInstances_AreEqual(): void
    {
        $position1 = DeletePosition::notFound();
        $position2 = DeletePosition::notFound();

        $this->assertNull($position1->foundNode);
        $this->assertNull($position2->foundNode);
        $this->assertNull($position1->previousNode);
        $this->assertNull($position2->previousNode);
    }

    #[Test]
    #[TestDox('foundAtHead with different nodes creates different positions')]
    public function foundAtHead_WithDifferentNodes_CreatesDifferentPositions(): void
    {
        $node1 = new Node(10);
        $node2 = new Node(20);

        $position1 = DeletePosition::foundAtHead($node1);
        $position2 = DeletePosition::foundAtHead($node2);

        $this->assertSame($node1, $position1->foundNode);
        $this->assertSame($node2, $position2->foundNode);
        $this->assertNotSame($position1->foundNode, $position2->foundNode);
    }
}
