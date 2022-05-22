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

final class DiffParserTest extends TestCase
{
    public function testParseCount(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $diff = $parser->parse($data);

        $this->assertInstanceOf(Changeset::class, $diff);
        $this->assertCount(5, $diff->files);
    }

    public function testFirstFile(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(1, $fileList[0]->hunks);

        $file = new File(
            'README.md',
            'README.md',
            File::CHANGED,
            [
                new Hunk(
                    3,
                    7,
                    3,
                    7,
                    "\n",
                    [
                        new Line(
                            3,
                            3,
                            Line::UNCHANGED,
                            'A simple VCS wrapper for PHP attempting to offer a consistent API across VCS tools.',
                            "\n"
                        ),
                        new Line(4, 4, Line::UNCHANGED, '', "\n"),
                        new Line(5, 5, Line::UNCHANGED, '', "\n"),
                        new Line(
                            6,
                            -1,
                            Line::REMOVED,
                            '[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)]',
                            "\n"
                        ),
                        new Line(
                            -1,
                            6,
                            Line::ADDED,
                            '[![Build Status](https://travis-ci.org/ptlis/vcs.png?branch=master)]',
                            "\n"
                        ),
                        new Line(7, 7, Line::UNCHANGED, '', "\n"),
                        new Line(8, 8, Line::UNCHANGED, '', "\n"),
                        new Line(9, 9, Line::UNCHANGED, '## Cautions', "\n")
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testSecondFile(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(1, $fileList[1]->hunks);

        $file = new File(
            'build/phpmd.xml',
            'build/phpmd.xml',
            File::CHANGED,
            [
                new Hunk(
                    1,
                    5,
                    1,
                    5,
                    "\n",
                    [
                        new Line(1, 1, Line::UNCHANGED, '<?xml version="1.0"?>', "\n"),
                        new Line(2, -1, Line::REMOVED, '<ruleset name="ConNeg"', "\n"),
                        new Line(-1, 2, Line::ADDED, '<ruleset name="VCS"', "\n"),
                        new Line(3, 3, Line::UNCHANGED, '         xmlns="http://pmd.sf.net/ruleset/1.0.0"', "\n"),
                        new Line(
                            4,
                            4,
                            Line::UNCHANGED,
                            '         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"',
                            "\n"
                        ),
                        new Line(
                            5,
                            5,
                            Line::UNCHANGED,
                            '         xsi:schemaLocation="http://pmd.sf.net/ruleset/1.0.0 http://pmd.sf.net/'
                            . 'ruleset_xml_schema.xsd"',
                            "\n"
                        )
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[1]);
    }

    public function testThirdFile(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(2, $fileList[2]->hunks);

        $file = new File(
            'src/Svn/SvnVcs.php',
            'src/Svn/SvnVcs.php',
            File::CHANGED,
            [
                new Hunk(
                    40,
                    8,
                    40,
                    11,
                    "\n",
                    [
                        new Line(40, 40, Line::UNCHANGED, '     * @param RepositoryConfig $repoConfig', "\n"),
                        new Line(41, 41, Line::UNCHANGED, '     * @param string $currentBranch', "\n"),
                        new Line(42, 42, Line::UNCHANGED, '     */', "\n"),
                        new Line(
                            43,
                            -1,
                            Line::REMOVED,
                            '    public function __construct(CommandExecutorInterface ' . '$executor, RepositoryConfig'
                            . ' $repoConfig, $currentBranch = \'trunk\')',
                            "\n"
                        ),
                        new Line(44, -1, Line::REMOVED, '    {', "\n"),
                        new Line(-1, 43, Line::ADDED, '    public function __construct(', "\n"),
                        new Line(-1, 44, Line::ADDED, '        CommandExecutorInterface $executor,', "\n"),
                        new Line(-1, 45, Line::ADDED, '        RepositoryConfig $repoConfig,', "\n"),
                        new Line(-1, 46, Line::ADDED, '        $currentBranch = \'trunk\'', "\n"),
                        new Line(-1, 47, Line::ADDED, '    ) {', "\n"),
                        new Line(45, 48, Line::UNCHANGED, '        $this->executor = $executor;', "\n"),
                        new Line(46, 49, Line::UNCHANGED, '        $this->repoConfig = $repoConfig;', "\n"),
                        new Line(47, 50, Line::UNCHANGED, '        $this->currentBranch = $currentBranch;', "\n")
                    ]
                ),
                new Hunk(
                    65,
                    7,
                    68,
                    7,
                    "\n",
                    [
                        new Line(65, 68, Line::UNCHANGED, '     */', "\n"),
                        new Line(66, 69, Line::UNCHANGED, '    public function changeBranch($branch)', "\n"),
                        new Line(67, 70, Line::UNCHANGED, '    {', "\n"),
                        new Line(
                            68,
                            -1,
                            Line::REMOVED,
                            '        if (!$this->meta->branchExists((string)$branch)) {',
                            "\n"
                        ),
                        new Line(
                            -1,
                            71,
                            Line::ADDED,
                            '        if (!$this->meta->branchExists((string)$branch) && $this->repoConfig->'
                            . 'getTrunkName() !== $branch) {',
                            "\n"
                        ),
                        new Line(
                            69,
                            72,
                            Line::UNCHANGED,
                            '            throw new \RuntimeException(\'Branch named "\' . $branch . \'" not found.\');',
                            "\n"
                        ),
                        new Line(70, 73, Line::UNCHANGED, '        }', "\n"),
                        new Line(71, 74, Line::UNCHANGED, '', "\n")
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[2]);
    }

    public function testFourthFile(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(1, $fileList[3]->hunks);

        $file = new File(
            'tests/RepositoryConfigTest.php',
            'tests/RepositoryConfigTest.php',
            File::CHANGED,
            [
                new Hunk(
                    10,
                    7,
                    10,
                    6,
                    "\n",
                    [
                        new Line(10, 10, Line::UNCHANGED, '', "\n"),
                        new Line(11, 11, Line::UNCHANGED, 'namespace ptlis\Vcs\Test;', "\n"),
                        new Line(12, 12, Line::UNCHANGED, '', "\n"),
                        new Line(13, -1, Line::REMOVED, '', "\n"),
                        new Line(14, 13, Line::UNCHANGED, 'use ptlis\Vcs\Svn\RepositoryConfig;', "\n"),
                        new Line(15, 14, Line::UNCHANGED, '', "\n"),
                        new Line(
                            16,
                            15,
                            Line::UNCHANGED,
                            'class RepositoryConfigTest extends \PHPUnit_Framework_TestCase',
                            "\n"
                        )
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[3]);
    }

    public function testFifthFile(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(2, $fileList[4]->hunks);

        $file = new File(
            'tests/Vcs/Git/ChangeBranchTest.php',
            'tests/Vcs/Git/ChangeBranchTest.php',
            File::CHANGED,
            [
                new Hunk(
                    48,
                    6,
                    48,
                    7,
                    "\n",
                    [
                        new Line(48, 48, Line::UNCHANGED, '            $commandExecutor->getArguments()', "\n"),
                        new Line(49, 49, Line::UNCHANGED, '        );', "\n"),
                        new Line(50, 50, Line::UNCHANGED, '    }', "\n"),
                        new Line(-1, 51, Line::ADDED, '', "\n"),
                        new Line(51, 52, Line::UNCHANGED, '    public function testBranchDoesntExist()', "\n"),
                        new Line(52, 53, Line::UNCHANGED, '    {', "\n"),
                        new Line(53, 54, Line::UNCHANGED, '        $this->setExpectedException(', "\n")
                    ]
                ),
                new Hunk(
                    68,
                    7,
                    69,
                    5,
                    "\n",
                    [
                        new Line(68, 69, Line::UNCHANGED, '        $vcs = new GitVcs($commandExecutor);', "\n"),
                        new Line(69, 70, Line::UNCHANGED, '', "\n"),
                        new Line(70, 71, Line::UNCHANGED, '        $vcs->changeBranch(\'feat-new-badness\');', "\n"),
                        new Line(71, -1, Line::REMOVED, '', "\n"),
                        new Line(72, -1, Line::REMOVED, '', "\n"),
                        new Line(73, 72, Line::UNCHANGED, '    }', "\n"),
                        new Line(74, 73, Line::UNCHANGED, '}', "\n"),
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[4]);
    }
}
