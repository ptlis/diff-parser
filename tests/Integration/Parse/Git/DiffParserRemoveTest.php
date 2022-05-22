<?php

declare(strict_types=1);

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Test\Integration\Parse\Git;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Changeset;
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Parse\UnifiedDiffParser;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;
use ptlis\DiffParser\Parse\GitDiffNormalizer;

final class DiffParserRemoveTest extends TestCase
{
    public function testParseCount(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new GitDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_remove');

        $diff = $parser->parse($data);

        $this->assertInstanceOf(Changeset::class, $diff);
        $this->assertCount(1, $diff->files);
    }

    public function testFileRemove(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new GitDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_remove');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(1, $fileList[0]->hunks);

        $file = new File(
            'README.md',
            '/dev/null',
            File::DELETED,
            [
                new Hunk(
                    1,
                    1,
                    0,
                    0,
                    "\n",
                    [
                        new Line(1, -1, Line::REMOVED, '# My project', '')
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }
}
