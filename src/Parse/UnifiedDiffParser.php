<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2017 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Parse;

use ptlis\DiffParser\Changeset;
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;

/**
 * Class that parses a unified diff into a data-structure for convenient manipulation.
 */
final class UnifiedDiffParser
{
    /** @var UnifiedDiffTokenizer */
    private $tokenizer;


    /**
     * Constructor.
     *
     * @param UnifiedDiffTokenizer $tokenizer
     */
    public function __construct(UnifiedDiffTokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * Parse an array of tokens out into an object graph.
     *
     * @param array $diffLineList
     *
     * @return Changeset
     */
    public function parse(array $diffLineList)
    {
        $tokenList = $this->tokenizer->tokenize($diffLineList);

        $fileList = array();
        $startIndex = 0;
        $tokenCount = count($tokenList);
        for ($i = 0; $i < $tokenCount; $i++) {

            // File begin
            if (Token::ORIGINAL_FILENAME === $tokenList[$i]->getType()) {
                $startIndex = $i;
            }

            // File end, hydrate object
            if ($this->fileEnd($tokenList, $i + 1, Token::ORIGINAL_FILENAME)) {
                $fileList[] = $this->parseFile(
                    array_slice($tokenList, $startIndex, ($i - $startIndex) + 1)
                );
            }
        }

        return new Changeset($fileList);
    }

    /**
     * Process the tokens for a single file, returning a File instance on success.
     *
     * @param Token[] $fileTokenList
     *
     * @return File
     */
    private function parseFile(array $fileTokenList)
    {
        $originalName = $fileTokenList[0]->getValue();
        $newName = $fileTokenList[1]->getValue();

        $hunkList = array();
        $startIndex = 0;

        $tokenCount = count($fileTokenList);
        for ($i = 2; $i < $tokenCount; $i++) {

            // Hunk begin
            if ($this->hunkStart($fileTokenList[$i])) {
                $startIndex = $i;
            }

            // End of file, hydrate object
            if ($i === count($fileTokenList) - 1) {
                $hunkList[] = $this->parseHunk(
                    array_slice($fileTokenList, $startIndex)
                );

            // End of hunk, hydrate object
            } elseif ($this->hunkStart($fileTokenList[$i + 1])) {
                $hunkList[] = $this->parseHunk(
                    array_slice($fileTokenList, $startIndex, $i - $startIndex + 1)
                );
            }
        }

        return new File(
            $originalName,
            $newName,
            $this->getFileOperation($fileTokenList),
            $hunkList
        );
    }

    /**
     * Parse out the contents of a hunk.
     *
     * @param Token[] $hunkTokenList
     *
     * @return Hunk
     */
    private function parseHunk(array $hunkTokenList)
    {
        list(
            $originalStart,
            $originalCount,
            $newStart,
            $newCount,
            $tokensReadCount
        ) = $this->getHunkMeta($hunkTokenList);

        $originalLineNo = $originalStart;
        $newLineNo = $newStart;
        $lineList = array();

        $tokenCount = count($hunkTokenList);
        for ($i = $tokensReadCount; $i < $tokenCount; $i++) {
            $operation = $this->mapLineOperation($hunkTokenList[$i]);

            $lineList[] = new Line(
                (Line::ADDED) === $operation ? Line::LINE_NOT_PRESENT : $originalLineNo,
                (Line::REMOVED) === $operation ? Line::LINE_NOT_PRESENT : $newLineNo,
                $operation,
                $hunkTokenList[$i]->getValue()
            );

            if (Line::ADDED === $operation) {
                $newLineNo++;

            } elseif (Line::REMOVED === $operation) {
                $originalLineNo++;

            } else {
                $originalLineNo++;
                $newLineNo++;
            }
        }

        return new Hunk(
            $originalStart,
            $originalCount,
            $newStart,
            $newCount,
            $lineList
        );
    }

    /**
     * Parse out hunk meta.
     *
     * @param Token[] $hunkTokenList
     *
     * @return array Containing Original Start, Original Count, New Start, New Count & number of tokens consumed.
     */
    private function getHunkMeta(array $hunkTokenList)
    {
        switch (true) {
            case Token::FILE_DELETION_LINE_COUNT === $hunkTokenList[0]->getType():
                $originalStart = 1;
                $originalCount = intval($hunkTokenList[0]->getValue());
                $newStart = intval($hunkTokenList[1]->getValue());
                $newCount = intval($hunkTokenList[2]->getValue());
                $tokensReadCount = 3;
                break;

            case Token::FILE_CREATION_LINE_COUNT === $hunkTokenList[2]->getType():
                $originalStart = intval($hunkTokenList[0]->getValue());
                $originalCount = intval($hunkTokenList[1]->getValue());
                $newStart = 1;
                $newCount = intval($hunkTokenList[2]->getValue());
                $tokensReadCount = 3;
                break;

            default:
                $originalStart = intval($hunkTokenList[0]->getValue());
                $originalCount = intval($hunkTokenList[1]->getValue());
                $newStart = intval($hunkTokenList[2]->getValue());
                $newCount = intval($hunkTokenList[3]->getValue());
                $tokensReadCount = 4;
                break;
        }

        return array(
            $originalStart,
            $originalCount,
            $newStart,
            $newCount,
            $tokensReadCount
        );
    }

    /**
     * Determine if we're at the end of a 'section' of tokens.
     *
     * @param Token[] $tokenList
     * @param int $nextLine
     * @param string $delimiterToken
     *
     * @return bool
     */
    private function fileEnd(array $tokenList, $nextLine, $delimiterToken)
    {
        return $nextLine == count($tokenList) || $delimiterToken === $tokenList[$nextLine]->getType();
    }

    /**
     * Returns true if the token indicates the start of a hunk.
     *
     * @param Token $token
     *
     * @return bool
     */
    private function hunkStart(Token $token)
    {
        return Token::HUNK_ORIGINAL_START === $token->getType()
            || Token::FILE_DELETION_LINE_COUNT === $token->getType();
    }

    /**
     * Maps between token representation of line operations and the correct const from the Line class.
     *
     * @param Token $token
     *
     * @return string
     */
    private function mapLineOperation(Token $token)
    {
        if (Token::SOURCE_LINE_ADDED === $token->getType()) {
            $operation = Line::ADDED;

        } elseif (Token::SOURCE_LINE_REMOVED === $token->getType()) {
            $operation = Line::REMOVED;

        } else {
            $operation = Line::UNCHANGED;
        }

        return $operation;
    }

    /**
     * Get the operation performed on the file (create, delete, change).
     *
     * @param Token[] $fileTokenList
     *
     * @return string One of class constants File::CREATED, File::DELETED, File::CHANGED
     */
    private function getFileOperation(array $fileTokenList)
    {

        $operation = File::CHANGED;
        if (
            Token::FILE_CREATION_LINE_COUNT === $fileTokenList[4]->getType()
            || (0 === $fileTokenList[2]->getValue() && (0 === $fileTokenList[2]->getValue()))
        ) {
            $operation = File::CREATED;

        } else if (
            Token::FILE_DELETION_LINE_COUNT === $fileTokenList[2]->getType()
            || (0 === $fileTokenList[4]->getValue() && (0 === $fileTokenList[5]->getValue()))
        ) {
            $operation = File::DELETED;
        }

        return $operation;
    }
}
