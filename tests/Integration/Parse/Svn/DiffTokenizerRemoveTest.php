<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Integration\Parse\Svn;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Parse\Token;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;
use ptlis\DiffParser\Parse\SvnDiffNormalizer;

final class DiffTokenizerRemoveTest extends TestCase
{
    public function testFileRemoveSingleLine(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new SvnDiffNormalizer());

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_remove_single_line');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertCount(7, $tokenList);

        $this->assertEquals(new Token(Token::ORIGINAL_FILENAME, 'README.md', "\n"), $tokenList[0]);
        $this->assertEquals(new Token(Token::NEW_FILENAME, 'README.md', "\n"), $tokenList[1]);

        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_ONE_LINE, '1', ''), $tokenList[2]);
        $this->assertEquals(new Token(Token::HUNK_NEW_START, '0', ''), $tokenList[3]);
        $this->assertEquals(new Token(Token::HUNK_NEW_COUNT, '0', "\n"), $tokenList[4]);

        $this->assertEquals(new Token(Token::SOURCE_LINE_REMOVED, '## Test', "\n"), $tokenList[5]);
        $this->assertEquals(new Token(Token::SOURCE_NO_NEWLINE_EOF, '\ No newline at end of file', ''), $tokenList[6]);
    }

    public function testFileRemoveMultiLine(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new SvnDiffNormalizer());

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_remove_multi_line');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertCount(9, $tokenList);

        $this->assertEquals(new Token(Token::ORIGINAL_FILENAME, 'bar', "\n"), $tokenList[0]);
        $this->assertEquals(new Token(Token::NEW_FILENAME, 'bar', "\n"), $tokenList[1]);

        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_START, '1', ''), $tokenList[2]);
        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_COUNT, '3', ''), $tokenList[3]);
        $this->assertEquals(new Token(Token::HUNK_NEW_START, '0', ''), $tokenList[4]);
        $this->assertEquals(new Token(Token::HUNK_NEW_COUNT, '0', "\n"), $tokenList[5]);

        $this->assertEquals(new Token(Token::SOURCE_LINE_REMOVED, '<?php', "\n"), $tokenList[6]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_REMOVED, '', "\n"), $tokenList[7]);
        $this->assertEquals(new Token(Token::SOURCE_LINE_REMOVED, 'echo \'different test\';', "\n"), $tokenList[8]);
    }
}
