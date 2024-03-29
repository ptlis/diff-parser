<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Unit;

use ptlis\DiffParser\Change\IntChange;
use ptlis\DiffParser\Change\StringChange;
use ptlis\DiffParser\Changeset;
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Test\ExpectDeprecationTestCase;

/**
 * @covers \ptlis\DiffParser\Changeset
 */
final class ChangesetTest extends ExpectDeprecationTestCase
{
    protected function buildChangeset(): Changeset
    {
        $lineList = [
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
                PHP_EOL
            )
        ];

        $hunkList = [
            new Hunk(new IntChange(3, 4), new IntChange(7, 6), PHP_EOL, $lineList)
        ];

        $files = [
            new File(
                new StringChange('README.md', 'README.md'),
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
        $this->assertEquals($changeset->files, $changeset->getFiles(), 'Value returned from deprecated method getFiles() must match value of files');
        $this->expectDeprecationNotice();
    }
}
