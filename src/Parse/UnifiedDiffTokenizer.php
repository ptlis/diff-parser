<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2017 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Parse;

/**
 * Tokenizer for working with unified diffs.
 */
final class UnifiedDiffTokenizer
{
    /**
     * Regex used to determine if the current line is a hunk start.
     *
     * The are four tokens encoded in one line; start line number of change (original & new) and number of lines changed
     * (original & new).
     */
    const HUNK_START_REGEX = "
        /^@@\s
            (?:
                (?:
                    \-(?<hunk_original_start>\d+)   # Start line no, original
                    ,
                    (?<hunk_original_count>\d+)     # Line count, original
                )
                |
                \-(?<file_deletion>\d+)             # Delete file
            )
            \s
            (?:
                (?:
                    \+(?<hunk_new_start>\d+)        # Start line no, new
                    ,
                    (?<hunk_new_count>\d+)          # Line count, new
                )
                |
                \+(?<file_creation>\d+)             # Create file
            )
        \s@@/x
    ";

    /** @var DiffNormalizerInterface */
    private $diffNormalizer;


    /**
     * Constructor.
     *
     * @param DiffNormalizerInterface $diffNormalizer
     */
    public function __construct(DiffNormalizerInterface $diffNormalizer)
    {
        $this->diffNormalizer = $diffNormalizer;
    }

    /**
     * Tokenize a unified diff
     *
     * @param array $diffLineList
     *
     * @return Token[]
     */
    public function tokenize(array $diffLineList)
    {
        $tokenList = array();
        $hasStarted = false;

        $lineCount = count($diffLineList);
        for ($i = 0; $i < $lineCount; $i++) {

            // First line of a file
            if ($this->isFileStart($diffLineList, $i)) {
                $hasStarted = true;

                $tokenList = array_merge(
                    $tokenList,
                    $this->getFilenameTokens($diffLineList, $i)
                );

                $i++;   // Skip next line - we know this is safe due to check for is file start

            // Only proceed once a file beginning has been found
            } elseif ($hasStarted) {

                $hunkTokens = $this->getHunkStartTokens($diffLineList[$i]);

                // We have found a hunk start, process hunk lines
                if (
                    count($hunkTokens)
                    && (
                        Token::HUNK_ORIGINAL_START === $hunkTokens[0]->getType()
                        || Token::FILE_DELETION_LINE_COUNT === $hunkTokens[0]->getType()
                    )
                ) {
                    $i++;

                    // Simple change
                    if (4 == count($hunkTokens)) {
                        $originalLineCount = $hunkTokens[1]->getValue();
                        $newLineCount = $hunkTokens[3]->getValue();

                    // File deletion
                    } elseif (Token::FILE_DELETION_LINE_COUNT === $hunkTokens[0]->getType()) {
                        $originalLineCount = $hunkTokens[0]->getValue();
                        $newLineCount = 0;

                    // File creation
                    } else {
                        $originalLineCount = 0;
                        $newLineCount = $hunkTokens[2]->getValue();
                    }

                    $changeTokens = $this->processHunk(
                        $originalLineCount,
                        $newLineCount,
                        $diffLineList,
                        $i
                    );

                    $tokenList = array_merge($tokenList, $hunkTokens, $changeTokens);
                }
            }
        }

        return $tokenList;
    }

    /**
     * Process a hunk.
     *
     * @param int $originalLineCount
     * @param int $newLineCount
     * @param string[] $diffLineList
     * @param int $currentLine
     *
     * @return array
     */
    private function processHunk($originalLineCount, $newLineCount, $diffLineList, &$currentLine)
    {
        $addedCount = 0;
        $removedCount = 0;
        $unchangedCount = 0;
        $tokenList = array();

        // Iterate until we have the correct number of original & new lines
        $lineCount = count($diffLineList);
        for ($i = $currentLine; $i < $lineCount; $i++) {

            $tokenList[] = $this->getHunkLineToken(
                $addedCount,
                $removedCount,
                $unchangedCount,
                $diffLineList[$i]
            );

            // We have reached the line count for original & new versions of hunk
            if (
                $removedCount + $unchangedCount == $originalLineCount
                && $addedCount + $unchangedCount == $newLineCount
            ) {
                break;
            }
        }

        return $tokenList;
    }

    /**
     * Returns true if the current line is the beginning of a file section.
     *
     * @param string[] $diffLineList
     * @param int $currentLine
     *
     * @return bool
     */
    private function isFileStart(array $diffLineList, $currentLine)
    {
        return $currentLine + 1 < count($diffLineList)
            && '---' === substr($diffLineList[$currentLine], 0, 3)
            && '+++' === substr($diffLineList[$currentLine + 1], 0, 3);
    }

    /**
     * Parses the hunk start into appropriate tokens.
     *
     * @param string $diffLine
     *
     * @return Token[]
     */
    private function getHunkStartTokens($diffLine)
    {
        $tokenList = array();

        if (preg_match(self::HUNK_START_REGEX, $diffLine, $matches)) {

            // File deletion
            if ($this->hasToken($matches, Token::FILE_DELETION_LINE_COUNT)) {
                $tokenList = array(
                    new Token(Token::FILE_DELETION_LINE_COUNT, intval($matches[Token::FILE_DELETION_LINE_COUNT])),
                    new Token(Token::HUNK_NEW_START, intval($matches[Token::HUNK_NEW_START])),
                    new Token(Token::HUNK_NEW_COUNT, intval($matches[Token::HUNK_NEW_COUNT]))
                );

            // File creation
            } elseif ($this->hasToken($matches, Token::FILE_CREATION_LINE_COUNT)) {
                $tokenList = array(
                    new Token(Token::HUNK_ORIGINAL_START, intval($matches[Token::HUNK_ORIGINAL_START])),
                    new Token(Token::HUNK_ORIGINAL_COUNT, intval($matches[Token::HUNK_ORIGINAL_COUNT])),
                    new Token(Token::FILE_CREATION_LINE_COUNT, intval($matches[Token::FILE_CREATION_LINE_COUNT])),
                );

            // Standard Case
            } else {
                $tokenList = array(
                    new Token(Token::HUNK_ORIGINAL_START, intval($matches[Token::HUNK_ORIGINAL_START])),
                    new Token(Token::HUNK_ORIGINAL_COUNT, intval($matches[Token::HUNK_ORIGINAL_COUNT])),
                    new Token(Token::HUNK_NEW_START, intval($matches[Token::HUNK_NEW_START])),
                    new Token(Token::HUNK_NEW_COUNT, intval($matches[Token::HUNK_NEW_COUNT]))
                );
            }
        }

        return $tokenList;
    }

    /**
     * Get tokens for original & new filenames.
     *
     * @param string[] $diffLineList
     * @param int $currentLine
     *
     * @return array
     */
    private function getFilenameTokens(array $diffLineList, $currentLine)
    {
        $filenameTokens = array();

        // Get hunk metadata
        $hunkTokens = $this->getHunkStartTokens($diffLineList[$currentLine+2]);

        // In some cases we may have a diff with no contents (e.g. diff of svn propedit)
        if (count($hunkTokens)) {
            // Simple change
            if (4 == count($hunkTokens)) {
                $originalFilename = $this->diffNormalizer->getFilename($diffLineList[$currentLine]);
                $newFilename = $this->diffNormalizer->getFilename($diffLineList[$currentLine + 1]);

            // File deletion
            } elseif (Token::FILE_DELETION_LINE_COUNT === $hunkTokens[0]->getType()) {
                $originalFilename = $this->diffNormalizer->getFilename($diffLineList[$currentLine]);
                $newFilename = '';

            // File creation
            } else {
                $originalFilename = '';
                $newFilename = $this->diffNormalizer->getFilename($diffLineList[$currentLine + 1]);
            }

            $filenameTokens = array(
                new Token(Token::ORIGINAL_FILENAME, $originalFilename),
                new Token(Token::NEW_FILENAME, $newFilename)
            );
        }

        return $filenameTokens;
    }

    /**
     * Get a single line for a hunk.
     *
     * @param int $addedCount
     * @param int $removedCount
     * @param int $unchangedCount
     * @param string $diffLine
     *
     * @return Token
     */
    private function getHunkLineToken(
        &$addedCount,
        &$removedCount,
        &$unchangedCount,
        $diffLine
    ) {

        // Line added
        if ('+' === substr($diffLine, 0, 1)) {
            $tokenType = Token::SOURCE_LINE_ADDED;
            $addedCount++;

            // Line removed
        } elseif ('-' === substr($diffLine, 0, 1)) {
            $tokenType = Token::SOURCE_LINE_REMOVED;
            $removedCount++;

            // Line unchanged
        } else {
            $tokenType = Token::SOURCE_LINE_UNCHANGED;
            $unchangedCount++;
        }

        return new Token($tokenType, $this->normalizeChangedLine($diffLine));
    }

    /**
     * Remove the prefixed '+', '-' or ' ' from a changed line of code.
     *
     * @param string $line
     *
     * @return string
     */
    private function normalizeChangedLine($line)
    {
        return substr($line, 1);
    }

    /**
     * Returns true if the token key was found in the list.
     *
     * @param string[] $matchList
     * @param string $tokenKey
     *
     * @return bool
     */
    private function hasToken(array $matchList, $tokenKey)
    {
        return array_key_exists($tokenKey, $matchList) && strlen($matchList[$tokenKey]);
    }
}
