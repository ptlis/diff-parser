<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Integration;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Parser;

/**
 * @covers \ptlis\DiffParser\Parser
 */
final class ParserTest extends TestCase
{
    public function testParseFileGitSuccess(): void
    {
        $filename = __DIR__ . '/Parse/Git/data/diff_add';

        $parser = new Parser();
        $changeset = $parser->parseFile($filename, Parser::VCS_GIT);

        $fileList = $changeset->files;

        $this->assertCount(1, $fileList[0]->hunks);

        $file = new File(
            '/dev/null',
            'README.md',
            File::CREATED,
            [
                new Hunk(
                    0,
                    0,
                    1,
                    1,
                    "\n",
                    [
                        new Line(-1, 1, Line::ADDED, '## Test', '')
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testParseFileSvnSuccess(): void
    {
        $filename = __DIR__ . '/Parse/Svn/data/diff_add_single_line';

        $parser = new Parser();
        $changeset = $parser->parseFile($filename, Parser::VCS_SVN);

        $fileList = $changeset->files;

        $this->assertCount(1, $fileList[0]->hunks);

        $file = new File(
            'README.md',
            'README.md',
            File::CREATED,
            [
                new Hunk(
                    0,
                    0,
                    1,
                    1,
                    "\n",
                    [
                        new Line(-1, 1, Line::ADDED, '## Test', '')
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testParseFileStandardSuccess(): void
    {
        $filename = __DIR__ . '/Parse/Git/data/diff_add';

        $parser = new Parser();
        $changeset = $parser->parseFile($filename);

        $fileList = $changeset->files;

        $this->assertCount(1, $fileList[0]->hunks);

        $file = new File(
            '/dev/null',
            'README.md',
            File::CREATED,
            [
                new Hunk(
                    0,
                    0,
                    1,
                    1,
                    "\n",
                    [
                        new Line(-1, 1, Line::ADDED, '## Test', '')
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testParseFileGitFileNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('File "' . __DIR__ . '/Parse/Git/data/diff_wibble' . '" not found.');

        $filename = __DIR__ . '/Parse/Git/data/diff_wibble';

        $parser = new Parser();
        $parser->parseFile($filename, Parser::VCS_GIT);
    }
}
