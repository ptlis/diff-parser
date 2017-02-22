<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2017 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Test\Integration\Parse\Git;

use ptlis\DiffParser\Parse\Token;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;
use ptlis\DiffParser\Parse\GitDiffNormalizer;

class DiffTokenizerRemoveTest extends \PHPUnit_Framework_TestCase
{
    public function testFileRemove()
    {
        $tokenizer = new UnifiedDiffTokenizer(new GitDiffNormalizer());

        $data = file(__DIR__ . '/data/diff_remove', FILE_IGNORE_NEW_LINES);

        $tokenList = $tokenizer->tokenize($data);

        $this->assertEquals(6, count($tokenList));

        $this->assertEquals(new Token(Token::ORIGINAL_FILENAME, 'README.md'), $tokenList[0]);
        $this->assertEquals(new Token(Token::NEW_FILENAME, ''), $tokenList[1]);

        $this->assertEquals(new Token(Token::FILE_DELETION_LINE_COUNT, 1), $tokenList[2]);
        $this->assertEquals(new Token(Token::HUNK_NEW_START, 0), $tokenList[3]);
        $this->assertEquals(new Token(Token::HUNK_NEW_COUNT, 0), $tokenList[4]);

        $this->assertEquals(new Token(Token::SOURCE_LINE_REMOVED, '# My project'), $tokenList[5]);
    }
}
