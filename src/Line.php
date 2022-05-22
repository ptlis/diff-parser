<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser;

/**
 * Class storing metadata about a single line from a hunk.
 *
 * @phpstan-type LineOperation Line::ADDED|Line::REMOVED|Line::UNCHANGED
 */
final class Line
{
    public const ADDED = 'added';
    public const REMOVED = 'removed';
    public const UNCHANGED = 'unchanged';

    public const LINE_NOT_PRESENT = -1;

    /**
     * @param int $originalLineNo The original line number (before change applied), -1 if not present.
     * @param int $newLineNo The new line number (after change applied), -1 if not present.
     * @param string $operation The operation performed on this line (one of class constants).
     * @param string $content The contents of this line.
     * @param string $terminator The line terminator (e.g. newline, carriage return & newline, empty string).
     * @phpstan-param LineOperation $operation
     */
    public function __construct(
        public readonly int $originalLineNo,
        public readonly int $newLineNo,
        public readonly string $operation,
        public readonly string $content,
        public readonly string $terminator
    ) {
    }

    /**
     * Get the original line number (before change applied), -1 if not present.
     *
     * @deprecated This data should be accessed via the $originalLineNo property.
     */
    public function getOriginalLineNo(): int
    {
        return $this->originalLineNo;
    }

    /**
     * Get the new line number (after change applied), -1 if not present.
     *
     * @deprecated This data should be accessed via the $newLineNo property.
     */
    public function getNewLineNo(): int
    {
        return $this->newLineNo;
    }

    /**
     * Get the operation performed (one of class constants).
     *
     * @deprecated This data should be accessed via the $operation property.
     *
     * @phpstan-return LineOperation
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * Get the content of the line.
     *
     * @deprecated This data should be accessed via the $content property.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Get the line terminator (e.g. newline, carriage return & newline, empty string).
     *
     * @deprecated This data should be accessed via the $terminator property.
     */
    public function getLineDelimiter(): string
    {
        return $this->terminator;
    }

    /**
     * Get the string representation of this line.
     */
    public function __toString(): string
    {
        $line = match ($this->operation) {
            self::ADDED => '+',
            self::REMOVED => '-',
            default => ' ',
        };

        $line .= $this->content . $this->terminator;

        if ('' === $this->terminator) {
            $line .= PHP_EOL . '\ No newline at end of file' . PHP_EOL;
        }

        return $line;
    }
}
