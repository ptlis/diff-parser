<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Unit;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;

/**
 * @covers \ptlis\DiffParser\File
 */
final class FileTest extends TestCase
{
    private function buildFile(): File
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

        return new File(
            'README.md',
            'README.md',
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
        $this->assertEquals('README.md', $file->originalFilename);
        $this->assertEquals('README.md', $file->newFilename);
        $this->assertEquals(File::CHANGED, $file->operation);
        $this->assertCount(1, $file->hunks);
        $this->assertOldMethodsReturnSameValuesAsProperties($file);
    }

    private function assertOldMethodsReturnSameValuesAsProperties(File $file): void
    {
        $this->assertTrue($file->originalFilename === $file->getOriginalFilename());
        $this->assertTrue($file->newFilename === $file->getNewFilename());
        $this->assertTrue($file->operation === $file->getOperation());
        $this->assertTrue($file->hunks === $file->getHunks());
    }
}
