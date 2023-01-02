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
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Test\ExpectDeprecationTestCase;

/**
 * @covers \ptlis\DiffParser\File
 */
final class FileTest extends ExpectDeprecationTestCase
{
    private function buildFile(): File
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
                new IntChange(6, -1),
                Line::REMOVED,
                '[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)]',
                PHP_EOL
            ),
            new Line(
                new IntChange(-1, 7),
                Line::ADDED,
                '[![Build Status](https://travis-ci.org/ptlis/vcs.png?branch=master)]',
                PHP_EOL
            ),
            new Line(
                new IntChange(7, -1),
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

        return new File(
            new StringChange('README.md', 'README.md'),
            File::CHANGED,
            $hunkList
        );
    }

    public function testFile(): void
    {
        $file = $this->buildFile();

        $fileString = implode(
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

        $this->assertEquals($fileString, $file->__toString());
        $this->assertEquals('README.md', $file->filename->original);
        $this->assertEquals('README.md', $file->filename->new);
        $this->assertEquals(File::CHANGED, $file->operation);
        $this->assertCount(1, $file->hunks);
        $this->assertDeprecatedMethodsReturnSameValuesAsProperties($file);
    }

    private function assertDeprecatedMethodsReturnSameValuesAsProperties(File $file): void
    {
        $this->assertEquals($file->filename->original, $file->originalFilename, 'Deprecated property originalFilename value must match value of filename->original');
        $this->assertEquals($file->filename->original, $file->getOriginalFilename(), 'Value returned from deprecated method getOriginalFilename() must match value of filename->original');
        $this->assertEquals($file->filename->new, $file->newFilename, 'Deprecated property newFilename value must match value of filename->new');
        $this->assertEquals($file->filename->new, $file->getNewFilename(), 'Value returned from deprecated method getNewFilename() must match value of filename->new');
        $this->assertEquals($file->operation, $file->getOperation(), 'Value returned from deprecated method getOperation() must match value of operation');
        $this->assertEquals($file->hunks, $file->getHunks(), 'Value returned from deprecated method getHunks() must match value of hunks');
        $this->expectDeprecationNotice();
    }
}
