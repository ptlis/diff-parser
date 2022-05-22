<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Parse;

/**
 * Tokenizer for working with unified diffs.
 */
final class UnifiedDiffTokenizer
{
    private const NO_NEWLINE_MARKER = '\ No newline at end of file';

    /**
     * Regex used to determine if the current line is a hunk start.
     *
     * There are four tokens encoded in one line; start line number of change (original & new) and number of lines
     *  changed (original & new).
     */
    private const HUNK_START_REGEX = "
        /^@@\s
            (?:
                (?:
                    \-(?<hunk_original_start>\d+)   # Start line no, original
                    ,
                    (?<hunk_original_count>\d+)     # Line count, original
                )
                |
                \-(?<hunk_original_one_line>1)      # Original file is one line
            )
            \s
            (?:
                (?:
                    \+(?<hunk_new_start>\d+)        # Start line no, new
                    ,
                    (?<hunk_new_count>\d+)          # Line count, new
                )
                |
                \+(?<hunk_new_one_line>1)           # New file is one line
            )
        \s@@/x
    ";

    public function __construct(
        private readonly DiffNormalizerInterface $diffNormalizer
    ) {
    }

    /**
     * Tokenize a unified diff
     *
     * @param string $patchFile
     *
     * @return array<Token>
     */
    public function tokenize(string $patchFile): array
    {
        $diffLineList = $this->splitFile($patchFile);

        $tokenList = [];
        $hasStarted = false;

        $lineCount = \count($diffLineList);
        for ($i = 0; $i < $lineCount; $i++) {
            // First line of a file
            if ($this->isFileStart($diffLineList, $i)) {
                $hasStarted = true;

                $tokenList = \array_merge(
                    $tokenList,
                    $this->getFilenameTokens($diffLineList, $i)
                );

                $i++;   // Skip next line - we know this is safe due to check for is file start

            // Only proceed once a file beginning has been found
            } elseif ($hasStarted) {
                [$hunkTokens, $i] = $this->getHunkTokens($diffLineList, $i);

                $tokenList = \array_merge($tokenList, $hunkTokens);
            }
        }

        return $tokenList;
    }

    /**
     * Splits a patch file by line delimiter (\n, \r or \r\n), returning an array of RawDiffLine instances.
     *
     * @param string $patchFile
     * @return array<RawDiffLine>
     */
    private function splitFile(string $patchFile): array
    {
        // Split with regex, tracking new line delimiter
        /** @var array<string> $diffLineList */
        $diffLineList = \preg_split('/(\r\n|\r|\n)/', $patchFile, -1, PREG_SPLIT_DELIM_CAPTURE);

        // Remove empty strings from end of file (it's a side effect of above method of splitting file into lines, and
        //  we don't want to remove all empty elements via PREG_SPLIT_NO_EMPTY)
        if ('' === $diffLineList[\count($diffLineList) - 1]) {
            \array_pop($diffLineList);
        }

        $rawLineList = [];
        $lineCount = \count($diffLineList);
        for ($i = 0; $i + 1 < $lineCount; $i += 2) {
            $rawLineList[] = new RawDiffLine($diffLineList[$i], $diffLineList[$i + 1]);
        }

        // Handle final line not having linebreak
        if ($i === $lineCount - 1) {
            $rawLineList[] = new RawDiffLine($diffLineList[$i], '');
        }

        return $rawLineList;
    }

    /**
     * Process a hunk.
     *
     * @param array<RawDiffLine> $diffLineList
     * @param int $currentLine
     *
     * @return array{array<Token>, int}
     */
    private function getHunkTokens(
        array $diffLineList,
        int $currentLine
    ): array {
        $tokenList = [];
        $hunkStartTokens = $this->getHunkStartTokens($diffLineList[$currentLine]);

        // We have found a hunk start, process hunk lines
        if ($this->isHunkStart($hunkStartTokens)) {
            $currentLine++;

            [$originalLineCount, $newLineCount] = $this->getHunkLineCounts($hunkStartTokens);
            $addedCount = 0;
            $removedCount = 0;
            $unchangedCount = 0;

            // Iterate until we have the correct number of original & new lines
            $lineCount = \count($diffLineList);
            for ($i = $currentLine; $i < $lineCount; $i++) {
                $tokenList[] = $this->getHunkLineToken(
                    $addedCount,
                    $removedCount,
                    $unchangedCount,
                    $diffLineList[$i]
                );

                // We have reached the line count for original & new versions of hunk
                if (
                    $removedCount + $unchangedCount === $originalLineCount
                    && $addedCount + $unchangedCount === $newLineCount
                ) {
                    // Check for trailing 'No newline at end of file'
                    if ($i < $lineCount - 1 && self::NO_NEWLINE_MARKER === $diffLineList[$i + 1]->content) {
                        $tokenList[] = new Token(Token::SOURCE_NO_NEWLINE_EOF, self::NO_NEWLINE_MARKER, '');
                    }
                    break;
                }
            }
        }

        return [
            \array_merge($hunkStartTokens, $tokenList),
            $currentLine
        ];
    }

    /**
     * @param array<Token> $hunkTokens
     * @return bool
     */
    private function isHunkStart(array $hunkTokens): bool
    {
        return (
            \count($hunkTokens)
            && (
                Token::HUNK_ORIGINAL_START === $hunkTokens[0]->type
                || Token::HUNK_ORIGINAL_ONE_LINE === $hunkTokens[0]->type
            )
        );
    }

    /**
     * @param array<Token> $hunkTokens
     * @return array<int>
     */
    private function getHunkLineCounts(array $hunkTokens): array
    {
        $originalLineCount = 0;
        $newLineCount = 0;
        foreach ($hunkTokens as $token) {
            if (Token::HUNK_ORIGINAL_COUNT === $token->type) {
                $originalLineCount = \intval($token->value);
            } elseif (Token::HUNK_ORIGINAL_ONE_LINE === $token->type) {
                $originalLineCount = 1;
            } elseif (Token::HUNK_NEW_COUNT === $token->type) {
                $newLineCount = \intval($token->value);
            } elseif (Token::HUNK_NEW_ONE_LINE === $token->type) {
                $newLineCount = 1;
            }
        }

        return [$originalLineCount, $newLineCount];
    }

    /**
     * Returns true if the current line is the beginning of a file section.
     *
     * @param array<RawDiffLine> $diffLineList
     * @param int $currentLine
     *
     * @return bool
     */
    private function isFileStart(array $diffLineList, int $currentLine): bool
    {
        return (
            $currentLine + 1 < \count($diffLineList)
            && \str_starts_with($diffLineList[$currentLine]->content, '---')
            && \str_starts_with($diffLineList[$currentLine + 1]->content, '+++')
        );
    }

    /**
     * Parses the hunk start into appropriate tokens.
     *
     * @param RawDiffLine $diffLine
     *
     * @return array<Token>
     */
    private function getHunkStartTokens(RawDiffLine $diffLine): array
    {
        $tokenList = [];

        if (\preg_match(self::HUNK_START_REGEX, $diffLine->content, $matches)) {
            if ($this->hasToken($matches, Token::HUNK_ORIGINAL_ONE_LINE)) {
                $tokenList[] = new Token(Token::HUNK_ORIGINAL_ONE_LINE, '1', '');
            } else {
                $tokenList[] = new Token(Token::HUNK_ORIGINAL_START, $matches[Token::HUNK_ORIGINAL_START], '');
                $tokenList[] = new Token(Token::HUNK_ORIGINAL_COUNT, $matches[Token::HUNK_ORIGINAL_COUNT], '');
            }
            if ($this->hasToken($matches, Token::HUNK_NEW_ONE_LINE)) {
                $tokenList[] = new Token(Token::HUNK_NEW_ONE_LINE, '1', $diffLine->lineTerminator);
            } else {
                $tokenList[] = new Token(Token::HUNK_NEW_START, $matches[Token::HUNK_NEW_START], '');
                $tokenList[] = new Token(
                    Token::HUNK_NEW_COUNT,
                    $matches[Token::HUNK_NEW_COUNT],
                    $diffLine->lineTerminator
                );
            }
        }

        return $tokenList;
    }

    /**
     * Get tokens for original & new filenames.
     *
     * @param array<RawDiffLine> $diffLineList
     * @param int $currentLine
     *
     * @return array<Token>
     */
    private function getFilenameTokens(array $diffLineList, int $currentLine): array
    {
        $filenameTokens = [];

        // Get hunk metadata
        $hunkTokens = $this->getHunkStartTokens($diffLineList[$currentLine + 2]);

        // In some cases we may have a diff with no contents (e.g. diff of svn propedit)
        if (\count($hunkTokens)) {
            $originalFilename = $this->diffNormalizer->getFilename($diffLineList[$currentLine]);
            $newFilename = $this->diffNormalizer->getFilename($diffLineList[$currentLine + 1]);

            $filenameTokens = [
                new Token(Token::ORIGINAL_FILENAME, $originalFilename, $diffLineList[$currentLine]->lineTerminator),
                new Token(Token::NEW_FILENAME, $newFilename, $diffLineList[$currentLine + 1]->lineTerminator)
            ];
        }

        return $filenameTokens;
    }

    /**
     * Get a single line for a hunk.
     */
    private function getHunkLineToken(
        int &$addedCount,
        int &$removedCount,
        int &$unchangedCount,
        RawDiffLine $diffLine
    ): Token {

        // Line added
        if (\str_starts_with($diffLine->content, '+')) {
            $tokenType = Token::SOURCE_LINE_ADDED;
            $changedLine = $this->normalizeChangedLine($diffLine->content);
            $addedCount++;

        // Line removed
        } elseif (\str_starts_with($diffLine->content, '-')) {
            $tokenType = Token::SOURCE_LINE_REMOVED;
            $changedLine = $this->normalizeChangedLine($diffLine->content);
            $removedCount++;

        // 'No newline at end of file'
        } elseif (self::NO_NEWLINE_MARKER === $diffLine->content) {
            $tokenType = Token::SOURCE_NO_NEWLINE_EOF;
            $changedLine = $diffLine->content;

        // Line unchanged
        } else {
            $tokenType = Token::SOURCE_LINE_UNCHANGED;
            $changedLine = $this->normalizeChangedLine($diffLine->content);
            $unchangedCount++;
        }

        return new Token($tokenType, $changedLine, $diffLine->lineTerminator);
    }

    /**
     * Remove the prefixed '+', '-' or ' ' from a changed line of code.
     */
    private function normalizeChangedLine(string $line): string
    {
        $normalized = \substr($line, 1);

        return !\is_string($normalized) ? $line : $normalized;
    }

    /**
     * Returns true if the token key was found in the list.
     *
     * @param array<string> $matchList
     * @param string $tokenKey
     *
     * @return bool
     */
    private function hasToken(array $matchList, string $tokenKey): bool
    {
        return \array_key_exists($tokenKey, $matchList) && \strlen($matchList[$tokenKey]);
    }
}
