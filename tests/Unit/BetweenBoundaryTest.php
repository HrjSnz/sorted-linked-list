<?php

declare(strict_types=1);

namespace HrjSnz\SortedLinkedList\Tests\Unit;

use HrjSnz\SortedLinkedList\BetweenBoundary;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(BetweenBoundary::class)]
final class BetweenBoundaryTest extends TestCase
{
    #[Test]
    #[TestDox('INCLUSIVE case exists and is accessible')]
    public function inclusive_CaseExists_IsAccessible(): void
    {
        $boundary = BetweenBoundary::INCLUSIVE;

        $this->assertSame(BetweenBoundary::INCLUSIVE, $boundary);
    }

    #[Test]
    #[TestDox('EXCLUSIVE case exists and is accessible')]
    public function exclusive_CaseExists_IsAccessible(): void
    {
        $boundary = BetweenBoundary::EXCLUSIVE;

        $this->assertSame(BetweenBoundary::EXCLUSIVE, $boundary);
    }

    #[Test]
    #[TestDox('Enum cases can be compared for equality')]
    public function enumCases_CanBeCompared_ForEquality(): void
    {
        $inclusive1 = BetweenBoundary::INCLUSIVE;
        $inclusive2 = BetweenBoundary::INCLUSIVE;
        $exclusive = BetweenBoundary::EXCLUSIVE;

        $this->assertTrue($inclusive1 === $inclusive2);
        $this->assertFalse($inclusive1 === $exclusive);
    }

    #[Test]
    #[TestDox('Enum can be used in match expressions')]
    public function enum_CanBeUsed_InMatchExpressions(): void
    {
        $result = match (BetweenBoundary::INCLUSIVE) {
            BetweenBoundary::INCLUSIVE => 'inclusive',
            BetweenBoundary::EXCLUSIVE => 'exclusive',
        };

        $this->assertSame('inclusive', $result);
    }

    #[Test]
    #[TestDox('Enum has exactly two cases')]
    public function enum_HasExactlyTwo_Cases(): void
    {
        $cases = BetweenBoundary::cases();

        $this->assertCount(2, $cases);
        $this->assertContains(BetweenBoundary::INCLUSIVE, $cases);
        $this->assertContains(BetweenBoundary::EXCLUSIVE, $cases);
    }

    #[Test]
    #[TestDox('Enum case name can be retrieved')]
    public function enumCase_NameCanBeRetrieved(): void
    {
        $this->assertSame('INCLUSIVE', BetweenBoundary::INCLUSIVE->name);
        $this->assertSame('EXCLUSIVE', BetweenBoundary::EXCLUSIVE->name);
    }
}
