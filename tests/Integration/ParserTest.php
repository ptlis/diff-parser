<?php declare(strict_types=1);

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

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

        $fileList = $changeset->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            '',
            'README.md',
            File::CREATED,
            [
                new Hunk(
                    0,
                    0,
                    1,
                    1,
                    [
                        new Line(-1, 1, Line::ADDED, '## Test')
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

        $fileList = $changeset->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            '',
            'README.md',
            File::CREATED,
            [
                new Hunk(
                    0,
                    0,
                    1,
                    1,
                    [
                        new Line(-1, 1, Line::ADDED, '## Test')
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

        $fileList = $changeset->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            '',
            'README.md',
            File::CREATED,
            [
                new Hunk(
                    0,
                    0,
                    1,
                    1,
                    [
                        new Line(-1, 1, Line::ADDED, '## Test')
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testParseLinesGitSuccess(): void
    {
        $filename = __DIR__ . '/Parse/Git/data/diff_add';

        $parser = new Parser();
        $changeset = $parser->parseLines(
            file($filename, FILE_IGNORE_NEW_LINES),
            Parser::VCS_GIT
        );

        $fileList = $changeset->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            '',
            'README.md',
            File::CREATED,
            [
                new Hunk(
                    0,
                    0,
                    1,
                    1,
                    [
                        new Line(-1, 1, Line::ADDED, '## Test')
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
