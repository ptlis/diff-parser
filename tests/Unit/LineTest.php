<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Unit;

use ptlis\DiffParser\Change\IntChange;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Test\ExpectDeprecationTestCase;

/**
 * @covers \ptlis\DiffParser\Line
 */
final class LineTest extends ExpectDeprecationTestCase
{
    public function testLineUnchanged(): void
    {
        $line = new Line(
            new IntChange(5, 6),
            Line::UNCHANGED,
            'bob',
            "\n"
        );

        $this->assertEquals(5, $line->number->original);
        $this->assertEquals(6, $line->number->new);
        $this->assertEquals(Line::UNCHANGED, $line->operation);
        $this->assertEquals('bob', $line->content);
        $this->assertEquals("\n", $line->terminator);
        $this->assertEquals(' bob' . "\n", $line->__toString());
        $this->assertDeprecatedMethodsReturnSameValuesAsProperties($line);
    }

    public function testLineRemoved(): void
    {
        $line = new Line(
            new IntChange(9, Line::LINE_NOT_PRESENT),
            Line::REMOVED,
            'some stuff',
            "\r\n"
        );

        $this->assertEquals(9, $line->number->original);
        $this->assertEquals(-1, $line->number->new);
        $this->assertEquals(Line::REMOVED, $line->operation);
        $this->assertEquals('some stuff', $line->content);
        $this->assertEquals("\r\n", $line->terminator);
        $this->assertEquals('-some stuff' . "\r\n", $line->__toString());
        $this->assertDeprecatedMethodsReturnSameValuesAsProperties($line);
    }

    public function testLineAdded(): void
    {
        $line = new Line(
            new IntChange(Line::LINE_NOT_PRESENT, 11),
            Line::ADDED,
            'really good comment',
            "\r"
        );

        $this->assertEquals(-1, $line->number->original);
        $this->assertEquals(11, $line->number->new);
        $this->assertEquals(Line::ADDED, $line->operation);
        $this->assertEquals('really good comment', $line->content);
        $this->assertEquals("\r", $line->terminator);
        $this->assertEquals('+really good comment' . "\r", $line->__toString());
        $this->assertDeprecatedMethodsReturnSameValuesAsProperties($line);
    }

    public function testNewlineOmitted(): void
    {
        $line = new Line(
            new IntChange(Line::LINE_NOT_PRESENT, 11),
            Line::ADDED,
            'really good comment',
            ''
        );

        $this->assertEquals(Line::LINE_NOT_PRESENT, $line->number->original);
        $this->assertEquals(11, $line->number->new);
        $this->assertEquals(Line::ADDED, $line->operation);
        $this->assertEquals('really good comment', $line->content);
        $this->assertEquals('', $line->terminator);
        $this->assertEquals('+really good comment' . "\n" . '\ No newline at end of file' . "\n", $line->__toString());
        $this->assertDeprecatedMethodsReturnSameValuesAsProperties($line);
    }

    private function assertDeprecatedMethodsReturnSameValuesAsProperties(Line $line): void
    {
        // Original line number
        $this->assertEquals($line->number->original, $line->originalLineNo, 'Deprecated property originalLineNo value must match value of number->original');
        $this->assertEquals($line->number->original, $line->getOriginalLineNo(), 'Value returned from deprecated method getOriginalLineNo() must match value of number->original');

        // New line number
        $this->assertEquals($line->number->new, $line->newLineNo, 'Deprecated property newLineNo value must match value of number->new');
        $this->assertEquals($line->number->new, $line->getNewLineNo(), 'Value returned from deprecated method getNewLineNo() must match value of number->new');

        $this->assertEquals($line->operation, $line->getOperation(), 'Value returned from deprecated method getOperation() must match value of operation');
        $this->assertEquals($line->content, $line->getContent(), 'Value returned from deprecated method getContent() must match value of content');
        $this->assertEquals($line->terminator, $line->getLineDelimiter(), 'Value returned from deprecated method getLineDelimiter() must match value of terminator');

        $this->expectDeprecationNotice();
    }
}
