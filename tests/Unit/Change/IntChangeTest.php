<?php

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Unit\Change;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Change\IntChange;

/**
 * @covers \ptlis\DiffParser\Change\IntChange
 */
final class IntChangeTest extends TestCase
{
    public function testCreateAndReadData(): void
    {
        $change = new IntChange(1, 99);
        $this->assertEquals(1, $change->original);
        $this->assertEquals(99, $change->new);
    }
}
