<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Integration\Parse\Svn;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Changeset;
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Parse\UnifiedDiffParser;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;
use ptlis\DiffParser\Parse\SvnDiffNormalizer;

final class DiffParserRemoveTest extends TestCase
{
    public function testParseCount(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_remove_single_line');

        $diff = $parser->parse($data);

        $this->assertInstanceOf(Changeset::class, $diff);
        $this->assertCount(1, $diff->files);
    }

    public function testFileRemoveSingleLinePre19(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_remove_single_line');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(1, $fileList[0]->hunks);

        $file = new File(
            'README.md',
            'README.md',
            File::DELETED,
            [
                new Hunk(
                    1,
                    1,
                    0,
                    0,
                    "\n",
                    [
                        new Line(1, -1, Line::REMOVED, '## Test', '')
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testFileRemovePost19(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_remove_1.9');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(1, $fileList[0]->hunks);

        $file = new File(
            'bar',
            '',
            File::DELETED,
            [
                new Hunk(
                    1,
                    3,
                    0,
                    0,
                    "\n",
                    [
                        new Line(1, -1, Line::REMOVED, '<?php', "\n"),
                        new Line(2, -1, Line::REMOVED, '', "\n"),
                        new Line(3, -1, Line::REMOVED, 'echo \'different test\';', "\n")
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testFileRemoveMultiLine(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_remove_multi_line');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(1, $fileList[0]->hunks);

        $file = new File(
            'bar',
            'bar',
            File::DELETED,
            [
                new Hunk(
                    1,
                    3,
                    0,
                    0,
                    "\n",
                    [
                        new Line(1, -1, Line::REMOVED, '<?php', "\n"),
                        new Line(2, -1, Line::REMOVED, '', "\n"),
                        new Line(3, -1, Line::REMOVED, 'echo \'different test\';', "\n")
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }
}
