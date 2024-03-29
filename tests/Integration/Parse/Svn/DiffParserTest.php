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
            new StringChange('README.md', 'README.md'),
            File::CHANGED,
            [
                new Hunk(
                    new IntChange(3, 3),
                    new IntChange(7, 7),
                    "\n",
                    [
                        new Line(
                            new IntChange(3, 3),
                            Line::UNCHANGED,
                            'A simple VCS wrapper for PHP attempting to offer a consistent API across VCS tools.',
                            "\n"
                        ),
                        new Line(new IntChange(4, 4), Line::UNCHANGED, '', "\n"),
                        new Line(new IntChange(5, 5), Line::UNCHANGED, '', "\n"),
                        new Line(
                            new IntChange(6, Line::LINE_NOT_PRESENT),
                            Line::REMOVED,
                            '[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)]',
                            "\n"
                        ),
                        new Line(
                            new IntChange(Line::LINE_NOT_PRESENT, 6),
                            Line::ADDED,
                            '[![Build Status](https://travis-ci.org/ptlis/vcs.png?branch=master)]',
                            "\n"
                        ),
                        new Line(new IntChange(7, 7), Line::UNCHANGED, '', "\n"),
                        new Line(new IntChange(8, 8), Line::UNCHANGED, '', "\n"),
                        new Line(new IntChange(9, 9), Line::UNCHANGED, '## Cautions', "\n")
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
            new StringChange('build/phpmd.xml', 'build/phpmd.xml'),
            File::CHANGED,
            [
                new Hunk(
                    new IntChange(1, 1),
                    new IntChange(5, 5),
                    "\n",
                    [
                        new Line(new IntChange(1, 1), Line::UNCHANGED, '<?xml version="1.0"?>', "\n"),
                        new Line(new IntChange(2, Line::LINE_NOT_PRESENT), Line::REMOVED, '<ruleset name="ConNeg"', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 2), Line::ADDED, '<ruleset name="VCS"', "\n"),
                        new Line(new IntChange(3, 3), Line::UNCHANGED, '         xmlns="http://pmd.sf.net/ruleset/1.0.0"', "\n"),
                        new Line(
                            new IntChange(4, 4),
                            Line::UNCHANGED,
                            '         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"',
                            "\n"
                        ),
                        new Line(
                            new IntChange(5, 5),
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
            new StringChange('src/Svn/SvnVcs.php', 'src/Svn/SvnVcs.php'),
            File::CHANGED,
            [
                new Hunk(
                    new IntChange(40, 40),
                    new IntChange(8, 11),
                    "\n",
                    [
                        new Line(new IntChange(40, 40), Line::UNCHANGED, '     * @param RepositoryConfig $repoConfig', "\n"),
                        new Line(new IntChange(41, 41), Line::UNCHANGED, '     * @param string $currentBranch', "\n"),
                        new Line(new IntChange(42, 42), Line::UNCHANGED, '     */', "\n"),
                        new Line(
                            new IntChange(43, Line::LINE_NOT_PRESENT),
                            Line::REMOVED,
                            '    public function __construct(CommandExecutorInterface ' . '$executor, RepositoryConfig'
                            . ' $repoConfig, $currentBranch = \'trunk\')',
                            "\n"
                        ),
                        new Line(new IntChange(44, Line::LINE_NOT_PRESENT), Line::REMOVED, '    {', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 43), Line::ADDED, '    public function __construct(', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 44), Line::ADDED, '        CommandExecutorInterface $executor,', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 45), Line::ADDED, '        RepositoryConfig $repoConfig,', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 46), Line::ADDED, '        $currentBranch = \'trunk\'', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 47), Line::ADDED, '    ) {', "\n"),
                        new Line(new IntChange(45, 48), Line::UNCHANGED, '        $this->executor = $executor;', "\n"),
                        new Line(new IntChange(46, 49), Line::UNCHANGED, '        $this->repoConfig = $repoConfig;', "\n"),
                        new Line(new IntChange(47, 50), Line::UNCHANGED, '        $this->currentBranch = $currentBranch;', "\n")
                    ]
                ),
                new Hunk(
                    new IntChange(65, 68),
                    new IntChange(7, 7),
                    "\n",
                    [
                        new Line(new IntChange(65, 68), Line::UNCHANGED, '     */', "\n"),
                        new Line(new IntChange(66, 69), Line::UNCHANGED, '    public function changeBranch($branch)', "\n"),
                        new Line(new IntChange(67, 70), Line::UNCHANGED, '    {', "\n"),
                        new Line(
                            new IntChange(68, Line::LINE_NOT_PRESENT),
                            Line::REMOVED,
                            '        if (!$this->meta->branchExists((string)$branch)) {',
                            "\n"
                        ),
                        new Line(
                            new IntChange(Line::LINE_NOT_PRESENT, 71),
                            Line::ADDED,
                            '        if (!$this->meta->branchExists((string)$branch) && $this->repoConfig->'
                            . 'getTrunkName() !== $branch) {',
                            "\n"
                        ),
                        new Line(
                            new IntChange(69, 72),
                            Line::UNCHANGED,
                            '            throw new \RuntimeException(\'Branch named "\' . $branch . \'" not found.\');',
                            "\n"
                        ),
                        new Line(new IntChange(70, 73), Line::UNCHANGED, '        }', "\n"),
                        new Line(new IntChange(71, 74), Line::UNCHANGED, '', "\n")
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
            new StringChange('tests/RepositoryConfigTest.php', 'tests/RepositoryConfigTest.php'),
            File::CHANGED,
            [
                new Hunk(
                    new IntChange(10, 10),
                    new IntChange(7, 6),
                    "\n",
                    [
                        new Line(new IntChange(10, 10), Line::UNCHANGED, '', "\n"),
                        new Line(new IntChange(11, 11), Line::UNCHANGED, 'namespace ptlis\Vcs\Test;', "\n"),
                        new Line(new IntChange(12, 12), Line::UNCHANGED, '', "\n"),
                        new Line(new IntChange(13, Line::LINE_NOT_PRESENT), Line::REMOVED, '', "\n"),
                        new Line(new IntChange(14, 13), Line::UNCHANGED, 'use ptlis\Vcs\Svn\RepositoryConfig;', "\n"),
                        new Line(new IntChange(15, 14), Line::UNCHANGED, '', "\n"),
                        new Line(
                            new IntChange(16, 15),
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
            new StringChange('tests/Vcs/Git/ChangeBranchTest.php', 'tests/Vcs/Git/ChangeBranchTest.php'),
            File::CHANGED,
            [
                new Hunk(
                    new IntChange(48, 48),
                    new IntChange(6, 7),
                    "\n",
                    [
                        new Line(new IntChange(48, 48), Line::UNCHANGED, '            $commandExecutor->getArguments()', "\n"),
                        new Line(new IntChange(49, 49), Line::UNCHANGED, '        );', "\n"),
                        new Line(new IntChange(50, 50), Line::UNCHANGED, '    }', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 51), Line::ADDED, '', "\n"),
                        new Line(new IntChange(51, 52), Line::UNCHANGED, '    public function testBranchDoesntExist()', "\n"),
                        new Line(new IntChange(52, 53), Line::UNCHANGED, '    {', "\n"),
                        new Line(new IntChange(53, 54), Line::UNCHANGED, '        $this->setExpectedException(', "\n")
                    ]
                ),
                new Hunk(
                    new IntChange(68, 69),
                    new IntChange(7, 5),
                    "\n",
                    [
                        new Line(new IntChange(68, 69), Line::UNCHANGED, '        $vcs = new GitVcs($commandExecutor);', "\n"),
                        new Line(new IntChange(69, 70), Line::UNCHANGED, '', "\n"),
                        new Line(new IntChange(70, 71), Line::UNCHANGED, '        $vcs->changeBranch(\'feat-new-badness\');', "\n"),
                        new Line(new IntChange(71, Line::LINE_NOT_PRESENT), Line::REMOVED, '', "\n"),
                        new Line(new IntChange(72, Line::LINE_NOT_PRESENT), Line::REMOVED, '', "\n"),
                        new Line(new IntChange(73, 72), Line::UNCHANGED, '    }', "\n"),
                        new Line(new IntChange(74, 73), Line::UNCHANGED, '}', "\n"),
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[4]);
    }
}
