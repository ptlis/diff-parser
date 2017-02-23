<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2017 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Test\Integration;

use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Parser;

/**
 * @covers \ptlis\DiffParser\Parser
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseFileGitSuccess()
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
            array(
                new Hunk(
                    0,
                    0,
                    1,
                    1,
                    array(
                        new Line(-1, 1, Line::ADDED, '## Test')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testParseFileSvnSuccess()
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
            array(
                new Hunk(
                    0,
                    0,
                    1,
                    1,
                    array(
                        new Line(-1, 1, Line::ADDED, '## Test')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testParseFileStandardSuccess()
    {
        $filename = __DIR__ . '/Parse/Git/data/diff_add';

        $parser = new Parser();
        $changeset = $parser->parseFile($filename);

        $fileList = $changeset->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            '',
            'b/README.md',
            File::CREATED,
            array(
                new Hunk(
                    0,
                    0,
                    1,
                    1,
                    array(
                        new Line(-1, 1, Line::ADDED, '## Test')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testParseLinesGitSuccess()
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
            array(
                new Hunk(
                    0,
                    0,
                    1,
                    1,
                    array(
                        new Line(-1, 1, Line::ADDED, '## Test')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testParseFileGitFileNotFound()
    {
        $this->setExpectedException(
            'RuntimeException',
            'File "' . __DIR__ . '/Parse/Git/data/diff_wibble' . '" not found.'
        );

        $filename = __DIR__ . '/Parse/Git/data/diff_wibble';

        $parser = new Parser();
        $parser->parseFile($filename, Parser::VCS_GIT);
    }
}
