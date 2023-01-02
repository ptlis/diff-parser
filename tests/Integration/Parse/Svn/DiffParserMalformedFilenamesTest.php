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

final class DiffParserMalformedFilenamesTest extends TestCase
{
    public function testParseCount(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_malformed_filenames');

        $diff = $parser->parse($data);

        $this->assertInstanceOf(Changeset::class, $diff);
        $this->assertCount(1, $diff->files);
    }

    public function testFileRemove(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_malformed_filenames');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(1, $fileList[0]->hunks);

        $file = new File(
            new StringChange('README.md', 'README.md'),
            File::CREATED,
            [
                new Hunk(
                    new IntChange(0, 1),
                    new IntChange(0, 1),
                    "\n",
                    [
                        new Line(new IntChange(-1, 1), Line::ADDED, '## Test', '')
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }
}
