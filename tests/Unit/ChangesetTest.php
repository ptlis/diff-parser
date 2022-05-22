<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Unit;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Changeset;
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;

/**
 * @covers \ptlis\DiffParser\Changeset
 */
final class ChangesetTest extends TestCase
{
    protected function buildChangeset(): Changeset
    {
        $lineList = [
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
                PHP_EOL
            )
        ];

        $hunkList = [
            new Hunk(
                3,
                7,
                4,
                6,
                PHP_EOL,
                $lineList
            )
        ];

        $files = [
            new File(
                'README.md',
                'README.md',
                File::CHANGED,
                $hunkList
            )
        ];

        return new Changeset($files);
    }

    public function testChangeset(): void
    {
        $changeset = $this->buildChangeset();

        $fileString = \implode(
            PHP_EOL,
            [
                '--- README.md',
                '+++ README.md',
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

        $this->assertEquals($fileString, $changeset->__toString());
        $this->assertCount(1, $changeset->files);
        $this->assertTrue($changeset->files === $changeset->getFiles());
    }
}
