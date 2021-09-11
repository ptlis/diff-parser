<?php declare(strict_types=1);

/**
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Integration\Test\Parse\Git;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Parse\UnifiedDiffParser;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;
use ptlis\DiffParser\Parse\GitDiffNormalizer;

final class DiffParserSingleLineFileTest extends TestCase
{
    public function testParseCount(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new GitDiffNormalizer()
            )
        );

        $data = file_get_contents(__DIR__ . '/data/diff_single_line_file');

        $diff = $parser->parse($data);

        $this->assertInstanceOf('ptlis\DiffParser\Changeset', $diff);
        $this->assertEquals(1, count($diff->getFiles()));
    }

    public function testFileEdited(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new GitDiffNormalizer()
            )
        );

        $data = file_get_contents(__DIR__ . '/data/diff_single_line_file');

        $diff = $parser->parse($data);
        $fileList = $diff->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            'test.txt',
            'test.txt',
            File::CHANGED,
            [
                new Hunk(
                    1,
                    1,
                    1,
                    1,
                    "\n",
                    [
                        new Line(1, -1, Line::REMOVED, 'test', "\n"),
                        new Line(-1, 1, Line::ADDED, 'edited', "\n")
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }
}
