<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Unit;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;

/**
 * @covers \ptlis\DiffParser\Hunk
 */
final class HunkTest extends TestCase
{
    private function buildHunk(bool $hasNewlineEof): Hunk
    {
        return new Hunk(
            3,
            7,
            4,
            6,
            PHP_EOL,
            [
                new Line(
                    3,
                    4,
                    Line::UNCHANGED,
                    'A simple VCS wrapper for PHP attempting to offer a consistent API across VCS tools.',
                    PHP_EOL
                ),
                new Line(
                    4,
                    5,
                    Line::UNCHANGED,
                    '',
                    PHP_EOL
                ),
                new Line(
                    5,
                    6,
                    Line::UNCHANGED,
                    '',
                    PHP_EOL
                ),
                new Line(
                    6,
                    -1,
                    Line::REMOVED,
                    '[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)]',
                    PHP_EOL
                ),
                new Line(
                    -1,
                    7,
                    Line::ADDED,
                    '[![Build Status](https://travis-ci.org/ptlis/vcs.png?branch=master)]',
                    PHP_EOL
                ),
                new Line(
                    7,
                    -1,
                    Line::REMOVED,
                    '',
                    PHP_EOL
                ),
                new Line(
                    8,
                    8,
                    Line::UNCHANGED,
                    '',
                    PHP_EOL
                ),
                new Line(
                    9,
                    9,
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
        $this->assertEquals(3, $hunk->originalStart);
        $this->assertEquals(7, $hunk->originalCount);
        $this->assertEquals(4, $hunk->newStart);
        $this->assertEquals(6, $hunk->newCount);
        $this->assertOldMethodsReturnSameValuesAsProperties($hunk);
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
        $this->assertEquals(3, $hunk->originalStart);
        $this->assertEquals(7, $hunk->originalCount);
        $this->assertEquals(4, $hunk->newStart);
        $this->assertEquals(6, $hunk->newCount);
        $this->assertOldMethodsReturnSameValuesAsProperties($hunk);
    }

    private function assertOldMethodsReturnSameValuesAsProperties(Hunk $hunk): void
    {
        $this->assertTrue($hunk->lines === $hunk->getLines());
        $this->assertTrue($hunk->originalStart === $hunk->getOriginalStart());
        $this->assertTrue($hunk->originalCount === $hunk->getOriginalCount());
        $this->assertTrue($hunk->newStart === $hunk->getNewStart());
        $this->assertTrue($hunk->newCount === $hunk->getNewCount());
    }
}
