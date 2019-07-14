<?php declare(strict_types=1);

/**
 * @copyright (c) 2014-present brian ridley
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
     * @param string $patchFile
     *
     * @return Changeset
     */
    public function parse(string $patchFile): Changeset
    {
        $tokenList = $this->tokenizer->tokenize($patchFile);

        $fileList = [];
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
    private function parseFile(array $fileTokenList): File
    {
        $originalName = $fileTokenList[0]->getValue();
        $newName = $fileTokenList[1]->getValue();

        $hunkList = [];
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
    private function parseHunk(array $hunkTokenList): Hunk
    {
        [$originalStart, $originalCount, $newStart, $newCount, $tokensReadCount, $metaDelimiter] = $this->getHunkMeta($hunkTokenList);
        $originalLineNo = $originalStart;
        $newLineNo = $newStart;
        $lineList = [];

        $tokenCount = count($hunkTokenList);
        for ($i = $tokensReadCount; $i < $tokenCount; $i++) {
            $currentToken = $hunkTokenList[$i];
            $operation = $this->mapLineOperation($currentToken);
            $lineDelimiter = $currentToken->getLineDelimiter();

            // If the next line is a 'No newline at end of file' then set an empty line terminator & skip next line
            if ($i + 1 <= $tokenCount - 1 && Token::SOURCE_NO_NEWLINE_EOF === $hunkTokenList[$i + 1]->getType()) {
                $lineDelimiter = '';
                $i++;
            }

            $lineList[] = new Line(
                (Line::ADDED) === $operation ? Line::LINE_NOT_PRESENT : $originalLineNo,
                (Line::REMOVED) === $operation ? Line::LINE_NOT_PRESENT : $newLineNo,
                $operation,
                $currentToken->getValue(),
                $lineDelimiter
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
            $metaDelimiter,
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
    private function getHunkMeta(array $hunkTokenList): array
    {
        switch (true) {
            case Token::FILE_DELETION_LINE_COUNT === $hunkTokenList[0]->getType():
                $originalStart = 1;
                $originalCount = intval($hunkTokenList[0]->getValue());
                $newStart = intval($hunkTokenList[1]->getValue());
                $newCount = intval($hunkTokenList[2]->getValue());
                $metaDelimiter = $hunkTokenList[2]->getLineDelimiter();
                $tokensReadCount = 3;
                break;

            case Token::FILE_CREATION_LINE_COUNT === $hunkTokenList[2]->getType():
                $originalStart = intval($hunkTokenList[0]->getValue());
                $originalCount = intval($hunkTokenList[1]->getValue());
                $newStart = 1;
                $newCount = intval($hunkTokenList[2]->getValue());
                $metaDelimiter = $hunkTokenList[2]->getLineDelimiter();
                $tokensReadCount = 3;
                break;

            default:
                $originalStart = intval($hunkTokenList[0]->getValue());
                $originalCount = intval($hunkTokenList[1]->getValue());
                $newStart = intval($hunkTokenList[2]->getValue());
                $newCount = intval($hunkTokenList[3]->getValue());
                $metaDelimiter = $hunkTokenList[3]->getLineDelimiter();
                $tokensReadCount = 4;
                break;
        }

        return [
            $originalStart,
            $originalCount,
            $newStart,
            $newCount,
            $tokensReadCount,
            $metaDelimiter
        ];
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
    private function fileEnd(array $tokenList, int $nextLine, string $delimiterToken): bool
    {
        return $nextLine == count($tokenList) || $delimiterToken === $tokenList[$nextLine]->getType();
    }

    /**
     * Returns true if the token indicates the start of a hunk.
     */
    private function hunkStart(Token $token): bool
    {
        return Token::HUNK_ORIGINAL_START === $token->getType()
            || Token::FILE_DELETION_LINE_COUNT === $token->getType();
    }

    /**
     * Maps between token representation of line operations and the correct const from the Line class.
     */
    private function mapLineOperation(Token $token): string
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
    private function getFileOperation(array $fileTokenList): string
    {
        $operation = File::CHANGED;
        if (
            Token::FILE_CREATION_LINE_COUNT === $fileTokenList[4]->getType()
            || ("0" === $fileTokenList[2]->getValue() && ("0" === $fileTokenList[2]->getValue()))
        ) {
            $operation = File::CREATED;
        } else if (
            Token::FILE_DELETION_LINE_COUNT === $fileTokenList[2]->getType()
            || ("0" === $fileTokenList[4]->getValue() && ("0" === $fileTokenList[5]->getValue()))
        ) {
            $operation = File::DELETED;
        }

        return $operation;
    }
}
