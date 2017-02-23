<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2017 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Test\Integration\Parse\Svn;

use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Parse\UnifiedDiffParser;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;
use ptlis\DiffParser\Parse\SvnDiffNormalizer;

class DiffParserRemoveTest extends \PHPUnit_Framework_TestCase
{
    public function testParseCount()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff_remove_single_line', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);

        $this->assertInstanceOf('ptlis\DiffParser\Changeset', $diff);
        $this->assertEquals(1, count($diff->getFiles()));
    }

    public function testFileRemoveSingleLinePre19()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff_remove_single_line', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);
        $fileList = $diff->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            'README.md',
            '',
            File::DELETED,
            array(
                new Hunk(
                    1,
                    1,
                    0,
                    0,
                    array(
                        new Line(1, -1, Line::REMOVED, '## Test')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testFileRemovePost19()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff_remove_1.9', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);
        $fileList = $diff->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            'bar',
            '',
            File::DELETED,
            array(
                new Hunk(
                    1,
                    3,
                    0,
                    0,
                    array(
                        new Line(1, -1, Line::REMOVED, '<?php'),
                        new Line(2, -1, Line::REMOVED, ''),
                        new Line(3, -1, Line::REMOVED, 'echo \'different test\';')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testFileRemoveMultiLine()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff_remove_multi_line', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);
        $fileList = $diff->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            'bar',
            'bar',
            File::DELETED,
            array(
                new Hunk(
                    1,
                    3,
                    0,
                    0,
                    array(
                        new Line(1, -1, Line::REMOVED, '<?php'),
                        new Line(2, -1, Line::REMOVED, ''),
                        new Line(3, -1, Line::REMOVED, 'echo \'different test\';')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[0]);
    }
}
