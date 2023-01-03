<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Unit;

use ptlis\DiffParser\Change\IntChange;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Test\ExpectDeprecationTestCase;

/**
 * @covers \ptlis\DiffParser\Hunk
 */
final class HunkTest extends ExpectDeprecationTestCase
{
    private function buildHunk(bool $hasNewlineEof): Hunk
    {
        return new Hunk(
            new IntChange(3, 4),
            new IntChange(7, 6),
            PHP_EOL,
            [
                new Line(
                    new IntChange(3, 4),
                    Line::UNCHANGED,
                    'A simple VCS wrapper for PHP attempting to offer a consistent API across VCS tools.',
                    PHP_EOL
                ),
                new Line(
                    new IntChange(4, 5),
                    Line::UNCHANGED,
                    '',
                    PHP_EOL
                ),
                new Line(
                    new IntChange(5, 6),
                    Line::UNCHANGED,
                    '',
                    PHP_EOL
                ),
                new Line(
                    new IntChange(6, Line::LINE_NOT_PRESENT),
                    Line::REMOVED,
                    '[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)]',
                    PHP_EOL
                ),
                new Line(
                    new IntChange(Line::LINE_NOT_PRESENT, 7),
                    Line::ADDED,
                    '[![Build Status](https://travis-ci.org/ptlis/vcs.png?branch=master)]',
                    PHP_EOL
                ),
                new Line(
                    new IntChange(7, Line::LINE_NOT_PRESENT),
                    Line::REMOVED,
                    '',
                    PHP_EOL
                ),
                new Line(
                    new IntChange(8, 8),
                    Line::UNCHANGED,
                    '',
                    PHP_EOL
                ),
                new Line(
                    new IntChange(9, 9),
                    Line::UNCHANGED,
                    '## Cautions',
                    $hasNewlineEof ? PHP_EOL : ''
                )
            ]
        );
    }

    public function testHunk(): void
    {
        $hunk = $this->buildHunk(true);

        $hunkString = implode(
            PHP_EOL,
            [
                '@@ -3,7 +4,6 @@',
                ' A simple VCS wrapper for PHP attempting to offer a consistent API across VCS tools.',
                ' ',
                ' ',
                '-[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)]',
                '+[![Build Status](https://travis-ci.org/ptlis/vcs.png?branch=master)]',
                '-',
                ' ',
                ' ## Cautions',
                ''
            ]
        );

        $this->assertEquals($hunkString, $hunk->__toString());
        $this->assertEquals(3, $hunk->startLine->original);
        $this->assertEquals(4, $hunk->startLine->new);
        $this->assertEquals(7, $hunk->affectedLines->original);
        $this->assertEquals(6, $hunk->affectedLines->new);
        $this->assertDeprecatedMethodsReturnSameValuesAsProperties($hunk);
    }

    public function testHunkNoNewlineEof(): void
    {
        $hunk = $this->buildHunk(false);

        $hunkString = implode(
            PHP_EOL,
            [
                '@@ -3,7 +4,6 @@',
                ' A simple VCS wrapper for PHP attempting to offer a consistent API across VCS tools.',
                ' ',
                ' ',
                '-[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)]',
                '+[![Build Status](https://travis-ci.org/ptlis/vcs.png?branch=master)]',
                '-',
                ' ',
                ' ## Cautions',
                '\ No newline at end of file',
                ''
            ]
        );

        $this->assertEquals($hunkString, $hunk->__toString());
        $this->assertEquals(3, $hunk->startLine->original);
        $this->assertEquals(4, $hunk->startLine->new);
        $this->assertEquals(7, $hunk->affectedLines->original);
        $this->assertEquals(6, $hunk->affectedLines->new);
        $this->assertDeprecatedMethodsReturnSameValuesAsProperties($hunk);
    }

    private function assertDeprecatedMethodsReturnSameValuesAsProperties(Hunk $hunk): void
    {
        // Original start line
        $this->assertEquals($hunk->startLine->original, $hunk->originalStart, 'Deprecated property originalStart value must match value of startLine->original');
        $this->assertEquals($hunk->startLine->original, $hunk->getOriginalStart(), 'Value returned from deprecated method getOriginalStart() must match value of startLine->original');

        // New start line
        $this->assertEquals($hunk->startLine->new, $hunk->newStart, 'Deprecated property newStart value must match value of startLine->new');
        $this->assertEquals($hunk->startLine->new, $hunk->getNewStart(), 'Value returned from deprecated method getOriginalStart() must match value of startLine->new');

        // Original affected lines count
        $this->assertEquals($hunk->affectedLines->original, $hunk->originalCount, 'Deprecated property originalCount value must match value of affectedLines->original');
        $this->assertEquals($hunk->affectedLines->original, $hunk->getOriginalCount(), 'Value returned from deprecated method getOriginalCount() must match value of affectedLines->original');

        // New affected lines count
        $this->assertEquals($hunk->affectedLines->new, $hunk->newCount, 'Deprecated property newCount value must match value of affectedLines->new');
        $this->assertEquals($hunk->affectedLines->new, $hunk->getNewCount(), 'Value returned from deprecated method getNewCount() must match value of affectedLines->new');

        $this->assertEquals($hunk->lines, $hunk->getLines(), 'Value returned from deprecated method getLines() must match value of lines');
        $this->expectDeprecationNotice();
    }
}
