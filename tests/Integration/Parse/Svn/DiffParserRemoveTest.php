<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Integration\Parse\Svn;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Change\IntChange;
use ptlis\DiffParser\Change\StringChange;
use ptlis\DiffParser\Changeset;
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Parse\UnifiedDiffParser;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;
use ptlis\DiffParser\Parse\SvnDiffNormalizer;

/**
 * @coversNothing
 */
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
            new StringChange('README.md', 'README.md'),
            File::DELETED,
            [
                new Hunk(
                    new IntChange(1, 0),
                    new IntChange(1, 0),
                    "\n",
                    [
                        new Line(new IntChange(1, Line::LINE_NOT_PRESENT), Line::REMOVED, '## Test', '')
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
            new StringChange('bar', ''),
            File::DELETED,
            [
                new Hunk(
                    new IntChange(1, 0),
                    new IntChange(3, 0),
                    "\n",
                    [
                        new Line(new IntChange(1, Line::LINE_NOT_PRESENT), Line::REMOVED, '<?php', "\n"),
                        new Line(new IntChange(2, Line::LINE_NOT_PRESENT), Line::REMOVED, '', "\n"),
                        new Line(new IntChange(3, Line::LINE_NOT_PRESENT), Line::REMOVED, 'echo \'different test\';', "\n")
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
            new StringChange('bar', 'bar'),
            File::DELETED,
            [
                new Hunk(
                    new IntChange(1, 0),
                    new IntChange(3, 0),
                    "\n",
                    [
                        new Line(new IntChange(1, Line::LINE_NOT_PRESENT), Line::REMOVED, '<?php', "\n"),
                        new Line(new IntChange(2, Line::LINE_NOT_PRESENT), Line::REMOVED, '', "\n"),
                        new Line(new IntChange(3, Line::LINE_NOT_PRESENT), Line::REMOVED, 'echo \'different test\';', "\n")
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }
}
