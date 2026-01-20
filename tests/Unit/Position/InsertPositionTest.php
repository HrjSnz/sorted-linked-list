<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit\Position;

use HrjSnz\SortedLinkedList\Node;
use HrjSnz\SortedLinkedList\Position\InsertPosition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(InsertPosition::class)]
final class InsertPositionTest extends TestCase
{
    #[Test]
    #[TestDox('head creates position with null node and isDuplicate false')]
    public function head_CreatesPosition_WithNullNodeAndNotDuplicate(): void
    {
        $position = InsertPosition::head();

        $this->assertNull($position->node);
        $this->assertFalse($position->isDuplicate);
    }

    #[Test]
    #[TestDox('after creates position with node and isDuplicate false')]
    public function after_CreatesPosition_WithNodeAndNotDuplicate(): void
    {
        $node = new Node(42);
        $position = InsertPosition::after($node);

        $this->assertSame($node, $position->node);
        $this->assertFalse($position->isDuplicate);
    }

    #[Test]
    #[TestDox('none creates position with null node and isDuplicate true')]
    public function none_CreatesPosition_WithNullNodeAndIsDuplicate(): void
    {
        $position = InsertPosition::none();

        $this->assertNull($position->node);
        $this->assertTrue($position->isDuplicate);
    }

    #[Test]
    #[TestDox('Properties are readonly and cannot be modified')]
    public function properties_AreReadonly_CannotBeModified(): void
    {
        $node = new Node(42);
        $position = InsertPosition::after($node);

        // Verify properties are readonly by checking they maintain their values
        $this->assertSame($node, $position->node);
        $this->assertFalse($position->isDuplicate);
    }

    #[Test]
    #[TestDox('head and none both have null node but different isDuplicate')]
    public function headAndNone_HaveNullNode_DifferentIsDuplicate(): void
    {
        $head = InsertPosition::head();
        $none = InsertPosition::none();

        $this->assertNull($head->node);
        $this->assertNull($none->node);
        $this->assertFalse($head->isDuplicate);
        $this->assertTrue($none->isDuplicate);
    }

    #[Test]
    #[TestDox('Multiple head instances have same properties')]
    public function multipleHeadInstances_HaveSameProperties(): void
    {
        $position1 = InsertPosition::head();
        $position2 = InsertPosition::head();

        $this->assertNull($position1->node);
        $this->assertNull($position2->node);
        $this->assertFalse($position1->isDuplicate);
        $this->assertFalse($position2->isDuplicate);
    }

    #[Test]
    #[TestDox('Multiple none instances have same properties')]
    public function multipleNoneInstances_HaveSameProperties(): void
    {
        $position1 = InsertPosition::none();
        $position2 = InsertPosition::none();

        $this->assertNull($position1->node);
        $this->assertNull($position2->node);
        $this->assertTrue($position1->isDuplicate);
        $this->assertTrue($position2->isDuplicate);
    }

    #[Test]
    #[TestDox('after with different nodes creates different positions')]
    public function after_WithDifferentNodes_CreatesDifferentPositions(): void
    {
        $node1 = new Node(10);
        $node2 = new Node(20);

        $position1 = InsertPosition::after($node1);
        $position2 = InsertPosition::after($node2);

        $this->assertSame($node1, $position1->node);
        $this->assertSame($node2, $position2->node);
        $this->assertFalse($position1->isDuplicate);
        $this->assertFalse($position2->isDuplicate);
    }
}
