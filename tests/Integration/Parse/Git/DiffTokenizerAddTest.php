<?php declare(strict_types=1);

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Test\Integration\Parse\Git;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Parse\Token;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;
use ptlis\DiffParser\Parse\GitDiffNormalizer;

final class DiffTokenizerAddTest extends TestCase
{
    public function testFileAdd(): void
    {
        $tokenizer = new UnifiedDiffTokenizer(new GitDiffNormalizer());

        $data = file_get_contents(__DIR__ . '/data/diff_add');

        $tokenList = $tokenizer->tokenize($data);

        $this->assertEquals(6, count($tokenList));

        $this->assertEquals(new Token(Token::ORIGINAL_FILENAME, '', "\n"), $tokenList[0]);
        $this->assertEquals(new Token(Token::NEW_FILENAME, 'README.md', "\n"), $tokenList[1]);

        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_START, '0', ''), $tokenList[2]);
        $this->assertEquals(new Token(Token::HUNK_ORIGINAL_COUNT, '0', ''), $tokenList[3]);
        $this->assertEquals(new Token(Token::FILE_CREATION_LINE_COUNT, '1', "\n"), $tokenList[4]);

        $this->assertEquals(new Token(Token::SOURCE_LINE_ADDED, '## Test', "\n"), $tokenList[5]);
    }
}
