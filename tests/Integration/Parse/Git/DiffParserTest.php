<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2017 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Integration\Test\Parse\Git;

use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Parse\UnifiedDiffParser;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;
use ptlis\DiffParser\Parse\GitDiffNormalizer;

class DiffParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseCount()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new GitDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);

        $this->assertInstanceOf('ptlis\DiffParser\Changeset', $diff);
        $this->assertEquals(5, count($diff->getFiles()));
    }

    public function testFirstFile()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new GitDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);
        $fileList = $diff->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            'README.md',
            'README.md',
            File::CHANGED,
            array(
                new Hunk(
                    3,
                    7,
                    3,
                    7,
                    array(
                        new Line(3, 3, Line::UNCHANGED, 'A simple VCS wrapper for PHP attempting to offer a consistent API across VCS tools.'),
                        new Line(4, 4, Line::UNCHANGED, ''),
                        new Line(5, 5, Line::UNCHANGED, ''),
                        new Line(6, -1, Line::REMOVED, '[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)](https://travis-ci.org/ptlis/vcs) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/vcs/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/vcs/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Latest Stable Version](https://poser.pugx.org/ptlis/vcs/v/stable.png)](https://packagist.org/packages/ptlis/vcs)'),
                        new Line(-1, 6, Line::ADDED, '[![Build Status](https://travis-ci.org/ptlis/vcs.png?branch=master)](https://travis-ci.org/ptlis/vcs) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/vcs/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/vcs/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Latest Stable Version](https://poser.pugx.org/ptlis/vcs/v/stable.png)](https://packagist.org/packages/ptlis/vcs)'),
                        new Line(7, 7, Line::UNCHANGED, ''),
                        new Line(8, 8, Line::UNCHANGED, ''),
                        new Line(9, 9, Line::UNCHANGED, '## Cautions')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testSecondFile()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new GitDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);
        $fileList = $diff->getFiles();

        $this->assertEquals(1, count($fileList[1]->getHunks()));

        $file = new File(
            'build/phpmd.xml',
            'build/phpmd.xml',
            File::CHANGED,
            array(
                new Hunk(
                    1,
                    5,
                    1,
                    5,
                    array(
                        new Line(1, 1, Line::UNCHANGED, '<?xml version="1.0"?>'),
                        new Line(2, -1, Line::REMOVED, '<ruleset name="ConNeg"'),
                        new Line(-1, 2, Line::ADDED, '<ruleset name="VCS"'),
                        new Line(3, 3, Line::UNCHANGED, '         xmlns="http://pmd.sf.net/ruleset/1.0.0"'),
                        new Line(4, 4, Line::UNCHANGED, '         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'),
                        new Line(5, 5, Line::UNCHANGED, '         xsi:schemaLocation="http://pmd.sf.net/ruleset/1.0.0 http://pmd.sf.net/ruleset_xml_schema.xsd"')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[1]);
    }

    public function testThirdFile()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new GitDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);
        $fileList = $diff->getFiles();

        $this->assertEquals(2, count($fileList[2]->getHunks()));

        $file = new File(
            'src/Svn/SvnVcs.php',
            'src/Svn/SvnVcs.php',
            File::CHANGED,
            array(
                new Hunk(
                    40,
                    8,
                    40,
                    11,
                    array(
                        new Line(40, 40, Line::UNCHANGED, '     * @param RepositoryConfig $repoConfig'),
                        new Line(41, 41, Line::UNCHANGED, '     * @param string $currentBranch'),
                        new Line(42, 42, Line::UNCHANGED, '     */'),
                        new Line(43, -1, Line::REMOVED, '    public function __construct(CommandExecutorInterface $executor, RepositoryConfig $repoConfig, $currentBranch = \'trunk\')'),
                        new Line(44, -1, Line::REMOVED, '    {'),
                        new Line(-1, 43, Line::ADDED, '    public function __construct('),
                        new Line(-1, 44, Line::ADDED, '        CommandExecutorInterface $executor,'),
                        new Line(-1, 45, Line::ADDED, '        RepositoryConfig $repoConfig,'),
                        new Line(-1, 46, Line::ADDED, '        $currentBranch = \'trunk\''),
                        new Line(-1, 47, Line::ADDED, '    ) {'),
                        new Line(45, 48, Line::UNCHANGED, '        $this->executor = $executor;'),
                        new Line(46, 49, Line::UNCHANGED, '        $this->repoConfig = $repoConfig;'),
                        new Line(47, 50, Line::UNCHANGED, '        $this->currentBranch = $currentBranch;')
                    )
                ),
                new Hunk(
                    65,
                    7,
                    68,
                    7,
                    array(
                        new Line(65, 68, Line::UNCHANGED, '     */'),
                        new Line(66, 69, Line::UNCHANGED, '    public function changeBranch($branch)'),
                        new Line(67, 70, Line::UNCHANGED, '    {'),
                        new Line(68, -1, Line::REMOVED, '        if (!$this->meta->branchExists((string)$branch)) {'),
                        new Line(-1, 71, Line::ADDED, '        if (!$this->meta->branchExists((string)$branch) && $this->repoConfig->getTrunkName() !== $branch) {'),
                        new Line(69, 72, Line::UNCHANGED, '            throw new \RuntimeException(\'Branch named "\' . $branch . \'" not found.\');'),
                        new Line(70, 73, Line::UNCHANGED, '        }'),
                        new Line(71, 74, Line::UNCHANGED, '')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[2]);
    }

    public function testFourthFile()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new GitDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);
        $fileList = $diff->getFiles();

        $this->assertEquals(1, count($fileList[3]->getHunks()));

        $file = new File(
            'tests/RepositoryConfigTest.php',
            'tests/RepositoryConfigTest.php',
            File::CHANGED,
            array(
                new Hunk(
                    10,
                    7,
                    10,
                    6,
                    array(
                        new Line(10, 10, Line::UNCHANGED, ''),
                        new Line(11, 11, Line::UNCHANGED, 'namespace ptlis\Vcs\Test;'),
                        new Line(12, 12, Line::UNCHANGED, ''),
                        new Line(13, -1, Line::REMOVED, ''),
                        new Line(14, 13, Line::UNCHANGED, 'use ptlis\Vcs\Svn\RepositoryConfig;'),
                        new Line(15, 14, Line::UNCHANGED, ''),
                        new Line(16, 15, Line::UNCHANGED, 'class RepositoryConfigTest extends \PHPUnit_Framework_TestCase')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[3]);
    }

    public function testFifthFile()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new GitDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);
        $fileList = $diff->getFiles();

        $this->assertEquals(2, count($fileList[4]->getHunks()));

        $file = new File(
            'tests/Vcs/Git/ChangeBranchTest.php',
            'tests/Vcs/Git/ChangeBranchTest.php',
            File::CHANGED,
            array(
                new Hunk(
                    48,
                    6,
                    48,
                    7,
                    array(
                        new Line(48, 48, Line::UNCHANGED, '            $commandExecutor->getArguments()'),
                        new Line(49, 49, Line::UNCHANGED, '        );'),
                        new Line(50, 50, Line::UNCHANGED, '    }'),
                        new Line(-1, 51, Line::ADDED, ''),
                        new Line(51, 52, Line::UNCHANGED, '    public function testBranchDoesntExist()'),
                        new Line(52, 53, Line::UNCHANGED, '    {'),
                        new Line(53, 54, Line::UNCHANGED, '        $this->setExpectedException(')
                    )
                ),
                new Hunk(
                    68,
                    7,
                    69,
                    5,
                    array(
                        new Line(68, 69, Line::UNCHANGED, '        $vcs = new GitVcs($commandExecutor);'),
                        new Line(69, 70, Line::UNCHANGED, ''),
                        new Line(70, 71, Line::UNCHANGED, '        $vcs->changeBranch(\'feat-new-badness\');'),
                        new Line(71, -1, Line::REMOVED, ''),
                        new Line(72, -1, Line::REMOVED, ''),
                        new Line(73, 72, Line::UNCHANGED, '    }'),
                        new Line(74, 73, Line::UNCHANGED, '}'),
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[4]);
    }
}
