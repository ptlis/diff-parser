<?php

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Unit\Change;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Change\StringChange;

/**
 * @covers \ptlis\DiffParser\Change\StringChange
 */
final class StringChangeTest extends TestCase
{
    public function testCreateAndReadData(): void
    {
        $change = new StringChange('foo', 'bar');
        $this->assertEquals('foo', $change->original);
        $this->assertEquals('bar', $change->new);
    }
}
