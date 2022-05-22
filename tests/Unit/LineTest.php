<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Unit;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Line;

/**
 * @covers \ptlis\DiffParser\Line
 */
final class LineTest extends TestCase
{
    public function testLineUnchanged(): void
    {
        $line = new Line(
            5,
            6,
            Line::UNCHANGED,
            'bob',
            "\n"
        );

        $this->assertEquals(5, $line->originalLineNo);
        $this->assertEquals(6, $line->newLineNo);
        $this->assertEquals(Line::UNCHANGED, $line->operation);
        $this->assertEquals('bob', $line->content);
        $this->assertEquals("\n", $line->terminator);
        $this->assertEquals(' bob' . "\n", $line->__toString());
        $this->assertOldMethodsReturnSameValuesAsProperties($line);
    }

    public function testLineRemoved(): void
    {
        $line = new Line(
            9,
            Line::LINE_NOT_PRESENT,
            Line::REMOVED,
            'some stuff',
            "\r\n"
        );

        $this->assertEquals(9, $line->originalLineNo);
        $this->assertEquals(-1, $line->newLineNo);
        $this->assertEquals(Line::REMOVED, $line->operation);
        $this->assertEquals('some stuff', $line->content);
        $this->assertEquals("\r\n", $line->terminator);
        $this->assertEquals('-some stuff' . "\r\n", $line->__toString());
        $this->assertOldMethodsReturnSameValuesAsProperties($line);
    }

    public function testLineAdded(): void
    {
        $line = new Line(
            Line::LINE_NOT_PRESENT,
            11,
            Line::ADDED,
            'really good comment',
            "\r"
        );

        $this->assertEquals(-1, $line->originalLineNo);
        $this->assertEquals(11, $line->newLineNo);
        $this->assertEquals(Line::ADDED, $line->operation);
        $this->assertEquals('really good comment', $line->content);
        $this->assertEquals("\r", $line->terminator);
        $this->assertEquals('+really good comment' . "\r", $line->__toString());
        $this->assertOldMethodsReturnSameValuesAsProperties($line);
    }

    public function testNewlineOmitted(): void
    {
        $line = new Line(
            Line::LINE_NOT_PRESENT,
            11,
            Line::ADDED,
            'really good comment',
            ''
        );

        $this->assertEquals(-1, $line->originalLineNo);
        $this->assertEquals(11, $line->newLineNo);
        $this->assertEquals(Line::ADDED, $line->operation);
        $this->assertEquals('really good comment', $line->content);
        $this->assertEquals('', $line->terminator);
        $this->assertEquals('+really good comment' . "\n" . '\ No newline at end of file' . "\n", $line->__toString());
        $this->assertOldMethodsReturnSameValuesAsProperties($line);
    }

    private function assertOldMethodsReturnSameValuesAsProperties(Line $line): void
    {
        $this->assertTrue($line->originalLineNo === $line->getOriginalLineNo());
        $this->assertTrue($line->newLineNo === $line->getNewLineNo());
        $this->assertTrue($line->operation === $line->getOperation());
        $this->assertTrue($line->content === $line->getContent());
        $this->assertTrue($line->terminator === $line->getLineDelimiter());
    }
}
