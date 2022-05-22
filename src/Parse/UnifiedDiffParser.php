<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Parse;

use ptlis\DiffParser\Changeset;
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;

/**
 * Class that parses a unified diff into a data-structure for convenient manipulation.
 *
 * @phpstan-type FileOperation File::CREATED|File::DELETED|File::CHANGED
 * @phpstan-type LineOperation Line::ADDED|Line::REMOVED|Line::UNCHANGED
 */
final class UnifiedDiffParser
{
    public function __construct(
        private readonly UnifiedDiffTokenizer $tokenizer
    ) {
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
        $tokenCount = \count($tokenList);
        for ($i = 0; $i < $tokenCount; $i++) {
            // File begin
            if (Token::ORIGINAL_FILENAME === $tokenList[$i]->type) {
                $startIndex = $i;
            }

            // File end, hydrate object
            if ($this->fileEnd($tokenList, $i + 1, Token::ORIGINAL_FILENAME)) {
                $fileList[] = $this->parseFile(
                    \array_slice($tokenList, $startIndex, ($i - $startIndex) + 1)
                );
            }
        }

        return new Changeset($fileList);
    }

    /**
     * Process the tokens for a single file, returning a File instance on success.
     *
     * @param array<Token> $fileTokenList
     *
     * @return File
     */
    private function parseFile(array $fileTokenList): File
    {
        $originalName = $fileTokenList[0]->value;
        $newName = $fileTokenList[1]->value;

        $hunkList = [];
        $startIndex = 0;

        $tokenCount = \count($fileTokenList);
        for ($i = 2; $i < $tokenCount; $i++) {
            // Hunk begin
            if ($this->hunkStart($fileTokenList[$i])) {
                $startIndex = $i;
            }

            // End of file, hydrate object
            if ($i === \count($fileTokenList) - 1) {
                $hunkList[] = $this->parseHunk(
                    \array_slice($fileTokenList, $startIndex)
                );

            // End of hunk, hydrate object
            } elseif ($this->hunkStart($fileTokenList[$i + 1])) {
                $hunkList[] = $this->parseHunk(
                    \array_slice($fileTokenList, $startIndex, $i - $startIndex + 1)
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
     * @param array<Token> $hunkTokenList
     *
     * @return Hunk
     */
    private function parseHunk(array $hunkTokenList): Hunk
    {
        $meta = $this->getHunkMeta($hunkTokenList);
        $originalLineNo = $meta->originalStart;
        $newLineNo = $meta->newStart;
        $lineList = [];

        $tokenCount = \count($hunkTokenList);
        for ($i = $meta->tokensReadCount; $i < $tokenCount; $i++) {
            $currentToken = $hunkTokenList[$i];
            $operation = $this->mapLineOperation($currentToken);
            $lineTerminator = $currentToken->lineTerminator;

            // If the next line is a 'No newline at end of file' then set an empty line terminator & skip next line
            if ($i + 1 <= $tokenCount - 1 && Token::SOURCE_NO_NEWLINE_EOF === $hunkTokenList[$i + 1]->type) {
                $lineTerminator = '';
                $i++;
            }

            $lineList[] = new Line(
                Line::ADDED === $operation ? Line::LINE_NOT_PRESENT : $originalLineNo,
                Line::REMOVED === $operation ? Line::LINE_NOT_PRESENT : $newLineNo,
                $operation,
                $currentToken->value,
                $lineTerminator
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
            $meta->originalStart,
            $meta->originalCount,
            $meta->newStart,
            $meta->newCount,
            $meta->lineTerminator,
            $lineList
        );
    }

    /**
     * Parse out hunk meta.
     *
     * @param array<Token> $hunkTokenList
     *
     * @return HunkMetaData
     */
    private function getHunkMeta(array $hunkTokenList): HunkMetaData
    {
        $tokensReadCount = 0;

        if (Token::HUNK_ORIGINAL_ONE_LINE === $hunkTokenList[$tokensReadCount]->type) {
            $originalStart = 1;
            $originalCount = 1;
            $tokensReadCount++;
        } else {
            $originalStart = \intval($hunkTokenList[$tokensReadCount++]->value);
            $originalCount = \intval($hunkTokenList[$tokensReadCount++]->value);
        }

        if (Token::HUNK_NEW_ONE_LINE === $hunkTokenList[$tokensReadCount]->type) {
            $newStart = 1;
            $newCount = 1;
            $tokensReadCount++;
        } else {
            $newStart = \intval($hunkTokenList[$tokensReadCount++]->value);
            $newCount = \intval($hunkTokenList[$tokensReadCount++]->value);
        }

        return new HunkMetaData(
            $originalStart,
            $originalCount,
            $newStart,
            $newCount,
            $tokensReadCount,
            $hunkTokenList[$tokensReadCount - 1]->lineTerminator
        );
    }

    /**
     * Determine if we're at the end of a 'section' of tokens.
     *
     * @param array<Token> $tokenList
     * @param int $nextLine
     * @param string $delimiterToken
     *
     * @return bool
     */
    private function fileEnd(array $tokenList, int $nextLine, string $delimiterToken): bool
    {
        return $nextLine == \count($tokenList) || $delimiterToken === $tokenList[$nextLine]->type;
    }

    /**
     * Returns true if the token indicates the start of a hunk.
     */
    private function hunkStart(Token $token): bool
    {
        return Token::HUNK_ORIGINAL_START === $token->type
            || Token::HUNK_ORIGINAL_ONE_LINE === $token->type;
    }

    /**
     * Maps between token representation of line operations and the correct const from the Line class.
     *
     * @phpstan-return LineOperation
     */
    private function mapLineOperation(Token $token): string
    {
        if (Token::SOURCE_LINE_ADDED === $token->type) {
            $operation = Line::ADDED;
        } elseif (Token::SOURCE_LINE_REMOVED === $token->type) {
            $operation = Line::REMOVED;
        } else {
            $operation = Line::UNCHANGED;
        }

        return $operation;
    }

    /**
     * Get the operation performed on the file (create, delete, change).
     *
     * @param array<Token> $fileTokenList
     *
     * @return string One of class constants File::CREATED, File::DELETED, File::CHANGED
     * @phpstan-return FileOperation
     */
    private function getFileOperation(array $fileTokenList): string
    {
        $operation = File::CHANGED;
        while (!$this->hunkStart($fileTokenList[0])) {
            \array_shift($fileTokenList);
        }
        $meta = $this->getHunkMeta($fileTokenList);
        if (0 === $meta->originalStart && 0 === $meta->originalCount) {
            $operation = File::CREATED;
        } elseif (0 === $meta->newStart && 0 === $meta->newCount) {
            $operation = File::DELETED;
        }

        return $operation;
    }
}
