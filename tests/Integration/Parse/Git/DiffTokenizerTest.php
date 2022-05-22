<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Integration\Parse\Git;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Parse\Token;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;
use ptlis\DiffParser\Parse\GitDiffNormalizer;

final class DiffTokenizerTest extends TestCase
{
    public function testTokenCount(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new GitDiffNormalizer());

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertCount(94, $tokenList);
    }

    public function testFirstFile(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new GitDiffNormalizer());

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertEquals(new Token(Token::ORIGINAL_FILENAME, 'README.md', "\n"), $tokenList[0]);
        $this->assertEquals(new Token(Token::NEW_FILENAME, 'README.md', "\n"), $tokenList[1]);

        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_START, '3', ''), $tokenList[2]);
        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_COUNT, '7', ''), $tokenList[3]);
        $this->assertEquals(new Token(Token::HUNK_NEW_START, '3', ''), $tokenList[4]);
        $this->assertEquals(new Token(Token::HUNK_NEW_COUNT, '7', "\n"), $tokenList[5]);

        $this->assertEquals(
            new Token(
                Token::SOURCE_LINE_UNCHANGED,
                'A simple VCS wrapper for PHP attempting to offer a consistent API across VCS tools.',
                "\n"
            ),
            $tokenList[6]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '', "\n"), $tokenList[7]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '', "\n"), $tokenList[8]);
        $this->assertEquals(
            new Token(
                Token::SOURCE_LINE_REMOVED,
                '[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)]',
                "\n"
            ),
            $tokenList[9]
        );
        $this->assertEquals(
            new Token(
                Token::SOURCE_LINE_ADDED,
                '[![Build Status](https://travis-ci.org/ptlis/vcs.png?branch=master)]',
                "\n"
            ),
            $tokenList[10]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '', "\n"), $tokenList[11]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '', "\n"), $tokenList[12]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '## Cautions', "\n"), $tokenList[13]);
    }

    public function testSecondFile(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new GitDiffNormalizer());

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertEquals(new Token(Token::ORIGINAL_FILENAME, 'build/phpmd.xml', "\n"), $tokenList[14]);
        $this->assertEquals(new Token(Token::NEW_FILENAME, 'build/phpmd.xml', "\n"), $tokenList[15]);

        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_START, '1', ''), $tokenList[16]);
        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_COUNT, '5', ''), $tokenList[17]);
        $this->assertEquals(new Token(Token::HUNK_NEW_START, '1', ''), $tokenList[18]);
        $this->assertEquals(new Token(Token::HUNK_NEW_COUNT, '5', "\n"), $tokenList[19]);

        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '<?xml version="1.0"?>', "\n"), $tokenList[20]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_REMOVED, '<ruleset name="ConNeg"', "\n"), $tokenList[21]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_ADDED, '<ruleset name="VCS"', "\n"), $tokenList[22]);
        $this->assertEquals(
            new Token(
                Token::SOURCE_LINE_UNCHANGED,
                '         xmlns="http://pmd.sf.net/ruleset/1.0.0"',
                "\n"
            ),
            $tokenList[23]
        );
        $this->assertEquals(
            new Token(
                Token::SOURCE_LINE_UNCHANGED,
                '         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"',
                "\n"
            ),
            $tokenList[24]
        );
        $this->assertEquals(
            new Token(
                Token::SOURCE_LINE_UNCHANGED,
                '         xsi:schemaLocation="http://pmd.sf.net/ruleset/1.0.0 http://pmd.sf.net/'
                . 'ruleset_xml_schema.xsd"',
                "\n"
            ),
            $tokenList[25]
        );
    }

    public function testThirdFileFirstChunk(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new GitDiffNormalizer());

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertEquals(new Token(Token::ORIGINAL_FILENAME, 'src/Svn/SvnVcs.php', "\n"), $tokenList[26]);
        $this->assertEquals(new Token(Token::NEW_FILENAME, 'src/Svn/SvnVcs.php', "\n"), $tokenList[27]);

        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_START, '40', ''), $tokenList[28]);
        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_COUNT, '8', ''), $tokenList[29]);
        $this->assertEquals(new Token(Token::HUNK_NEW_START, '40', ''), $tokenList[30]);
        $this->assertEquals(new Token(Token::HUNK_NEW_COUNT, '11', "\n"), $tokenList[31]);

        $this->assertEquals(
            new Token(Token::SOURCE_LINE_UNCHANGED, '     * @param RepositoryConfig $repoConfig', "\n"),
            $tokenList[32]
        );
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_UNCHANGED, '     * @param string $currentBranch', "\n"),
            $tokenList[33]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '     */', "\n"), $tokenList[34]);
        $this->assertEquals(
            new Token(
                Token::SOURCE_LINE_REMOVED,
                '    public function __construct(CommandExecutorInterface $executor, RepositoryConfig $repoConfig, '
                . '$currentBranch = \'trunk\')',
                "\n"
            ),
            $tokenList[35]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_REMOVED, '    {', "\n"), $tokenList[36]);
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_ADDED, '    public function __construct(', "\n"),
            $tokenList[37]
        );
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_ADDED, '        CommandExecutorInterface $executor,', "\n"),
            $tokenList[38]
        );
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_ADDED, '        RepositoryConfig $repoConfig,', "\n"),
            $tokenList[39]
        );
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_ADDED, '        $currentBranch = \'trunk\'', "\n"),
            $tokenList[40]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_ADDED, '    ) {', "\n"), $tokenList[41]);
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_UNCHANGED, '        $this->executor = $executor;', "\n"),
            $tokenList[42]
        );
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_UNCHANGED, '        $this->repoConfig = $repoConfig;', "\n"),
            $tokenList[43]
        );
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_UNCHANGED, '        $this->currentBranch = $currentBranch;', "\n"),
            $tokenList[44]
        );
    }

    public function testThirdFileSecondChunk(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new GitDiffNormalizer());

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_START, '65', ''), $tokenList[45]);
        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_COUNT, '7', ''), $tokenList[46]);
        $this->assertEquals(new Token(Token::HUNK_NEW_START, '68', ''), $tokenList[47]);
        $this->assertEquals(new Token(Token::HUNK_NEW_COUNT, '7', "\n"), $tokenList[48]);

        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '     */', "\n"), $tokenList[49]);
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_UNCHANGED, '    public function changeBranch($branch)', "\n"),
            $tokenList[50]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '    {', "\n"), $tokenList[51]);
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_REMOVED, '        if (!$this->meta->branchExists((string)$branch)) {', "\n"),
            $tokenList[52]
        );
        $this->assertEquals(
            new Token(
                Token::SOURCE_LINE_ADDED,
                '        if (!$this->meta->branchExists((string)$branch) && $this->repoConfig->getTrunkName() !== '
                . '$branch) {',
                "\n"
            ),
            $tokenList[53]
        );
        $this->assertEquals(
            new Token(
                Token::SOURCE_LINE_UNCHANGED,
                '            throw new \RuntimeException(\'Branch named "\' . $branch . \'" not found.\');',
                "\n"
            ),
            $tokenList[54]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '        }', "\n"), $tokenList[55]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '', "\n"), $tokenList[56]);
    }

    public function testFourthFile(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new GitDiffNormalizer());

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertEquals(
            new Token(Token::ORIGINAL_FILENAME, 'tests/RepositoryConfigTest.php', "\n"),
            $tokenList[57]
        );
        $this->assertEquals(new Token(Token::NEW_FILENAME, 'tests/RepositoryConfigTest.php', "\n"), $tokenList[58]);

        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_START, '10', ''), $tokenList[59]);
        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_COUNT, '7', ''), $tokenList[60]);
        $this->assertEquals(new Token(Token::HUNK_NEW_START, '10', ''), $tokenList[61]);
        $this->assertEquals(new Token(Token::HUNK_NEW_COUNT, '6', "\n"), $tokenList[62]);

        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '', "\n"), $tokenList[63]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, 'namespace ptlis\Vcs\Test;', "\n"), $tokenList[64]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '', "\n"), $tokenList[65]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_REMOVED, '', "\n"), $tokenList[66]);
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_UNCHANGED, 'use ptlis\Vcs\Svn\RepositoryConfig;', "\n"),
            $tokenList[67]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '', "\n"), $tokenList[68]);
        $this->assertEquals(
            new Token(
                Token::SOURCE_LINE_UNCHANGED,
                'class RepositoryConfigTest extends \PHPUnit_Framework_TestCase',
                "\n"
            ),
            $tokenList[69]
        );
    }

    public function testFifthFileFirstChunk(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new GitDiffNormalizer());

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertEquals(
            new Token(Token::ORIGINAL_FILENAME, 'tests/Vcs/Git/ChangeBranchTest.php', "\n"),
            $tokenList[70]
        );
        $this->assertEquals(new Token(Token::NEW_FILENAME, 'tests/Vcs/Git/ChangeBranchTest.php', "\n"), $tokenList[71]);

        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_START, '48', ''), $tokenList[72]);
        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_COUNT, '6', ''), $tokenList[73]);
        $this->assertEquals(new Token(Token::HUNK_NEW_START, '48', ''), $tokenList[74]);
        $this->assertEquals(new Token(Token::HUNK_NEW_COUNT, '7', "\n"), $tokenList[75]);

        $this->assertEquals(
            new Token(Token::SOURCE_LINE_UNCHANGED, '            $commandExecutor->getArguments()', "\n"),
            $tokenList[76]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '        );', "\n"), $tokenList[77]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '    }', "\n"), $tokenList[78]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_ADDED, '', "\n"), $tokenList[79]);
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_UNCHANGED, '    public function testBranchDoesntExist()', "\n"),
            $tokenList[80]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '    {', "\n"), $tokenList[81]);
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_UNCHANGED, '        $this->setExpectedException(', "\n"),
            $tokenList[82]
        );
    }

    public function testFifthFileSecondChunk(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new GitDiffNormalizer());

        $data = (string)\file_get_contents(__DIR__ . '/data/diff');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_START, '68', ''), $tokenList[83]);
        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_COUNT, '7', ''), $tokenList[84]);
        $this->assertEquals(new Token(Token::HUNK_NEW_START, '69', ''), $tokenList[85]);
        $this->assertEquals(new Token(Token::HUNK_NEW_COUNT, '5', "\n"), $tokenList[86]);

        $this->assertEquals(
            new Token(Token::SOURCE_LINE_UNCHANGED, '        $vcs = new GitVcs($commandExecutor);', "\n"),
            $tokenList[87]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '', "\n"), $tokenList[88]);
        $this->assertEquals(
            new Token(Token::SOURCE_LINE_UNCHANGED, '        $vcs->changeBranch(\'feat-new-badness\');', "\n"),
            $tokenList[89]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_REMOVED, '', "\n"), $tokenList[90]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_REMOVED, '', "\n"), $tokenList[91]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '    }', "\n"), $tokenList[92]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, '}', "\n"), $tokenList[93]);
    }

    public function testNewNoLineDelimiterAtEndOfFile(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new GitDiffNormalizer());

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_new_no_newline_at_end_of_file');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertCount(14, $tokenList);

        $this->assertEquals(new Token(Token::ORIGINAL_FILENAME, 'file.php', "\n"), $tokenList[0]);
        $this->assertEquals(new Token(Token::NEW_FILENAME, 'file.php', "\n"), $tokenList[1]);

        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_START, '1', ''), $tokenList[2]);
        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_COUNT, '3', ''), $tokenList[3]);
        $this->assertEquals(new Token(Token::HUNK_NEW_START, '1', ''), $tokenList[4]);
        $this->assertEquals(new Token(Token::HUNK_NEW_COUNT, '6', "\n"), $tokenList[5]);

        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, 'first line', "\n"), $tokenList[6]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, 'second line', "\n"), $tokenList[7]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_REMOVED, 'third line', "\n"), $tokenList[8]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_ADDED, 'test line', "\n"), $tokenList[9]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_ADDED, 'fourth line', "\n"), $tokenList[10]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_ADDED, '', "\n"), $tokenList[11]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_ADDED, 'some other line', "\n"), $tokenList[12]);
        $this->assertEquals(new Token(Token::SOURCE_NO_NEWLINE_EOF, '\ No newline at end of file', ''), $tokenList[13]);
    }

    public function testOriginalNoLineDelimiterAtEndOfFile(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new GitDiffNormalizer());

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_original_no_newline_at_end_of_file');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertCount(14, $tokenList);

        $this->assertEquals(new Token(Token::ORIGINAL_FILENAME, 'file.php', "\n"), $tokenList[0]);
        $this->assertEquals(new Token(Token::NEW_FILENAME, 'file.php', "\n"), $tokenList[1]);

        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_START, '1', ''), $tokenList[2]);
        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_COUNT, '3', ''), $tokenList[3]);
        $this->assertEquals(new Token(Token::HUNK_NEW_START, '1', ''), $tokenList[4]);
        $this->assertEquals(new Token(Token::HUNK_NEW_COUNT, '6', "\n"), $tokenList[5]);

        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, 'first line', "\n"), $tokenList[6]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_UNCHANGED, 'second line', "\n"), $tokenList[7]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_REMOVED, 'third line', "\n"), $tokenList[8]);
        $this->assertEquals(
            new Token(Token::SOURCE_NO_NEWLINE_EOF, '\ No newline at end of file', "\n"),
            $tokenList[9]
        );
        $this->assertEquals(new Token(Token::SOURCE_LINE_ADDED, 'test line', "\n"), $tokenList[10]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_ADDED, 'fourth line', "\n"), $tokenList[11]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_ADDED, '', "\n"), $tokenList[12]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_ADDED, 'some other line', ''), $tokenList[13]);
    }
}
