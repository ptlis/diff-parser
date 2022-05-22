<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser;

/**
 * Class storing data about hunks in changed files.
 */
final class Hunk
{
    /**
     * @param int $originalStart The original starting line.
     * @param int $originalCount The original line count.
     * @param int $newStart The new starting line.
     * @param int $newCount The new line count.
     * @param string $metaLineDelimiter Delimiter between the 'meta' line (file offsets) and the hunk data.
     * @param array<Line> $lines Array of lines in this hunk.
     */
    public function __construct(
        public readonly int $originalStart,
        public readonly int $originalCount,
        public readonly int $newStart,
        public readonly int $newCount,
        public readonly string $metaLineDelimiter,
        public readonly array $lines
    ) {
    }

    /**
     * Get the original starting line.
     *
     * @deprecated This data should be accessed via the $originalStart property.
     */
    public function getOriginalStart(): int
    {
        return $this->originalStart;
    }

    /**
     * Get the original number of lines.
     *
     * @deprecated This data should be accessed via the $originalCount property.
     */
    public function getOriginalCount(): int
    {
        return $this->originalCount;
    }

    /**
     * Get het new Start line.
     *
     * @deprecated This data should be accessed via the $newStart property.
     */
    public function getNewStart(): int
    {
        return $this->newStart;
    }

    /**
     * Get the new number of lines.
     *
     * @deprecated This data should be accessed via the $newCount property.
     */
    public function getNewCount(): int
    {
        return $this->newCount;
    }

    /**
     * Get the lines for this hunk.
     *
     * @deprecated This data should be accessed via the $lineList property.
     *
     * @return array<Line>
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * Get the string representation of this hunk.
     */
    public function __toString(): string
    {
        $string = \implode(
            '',
            [
                '@@ -',
                $this->originalStart,
                ',',
                $this->originalCount,
                ' +',
                $this->newStart,
                ',',
                $this->newCount,
                ' @@',
                $this->metaLineDelimiter
            ]
        );

        $string .= \implode('', $this->lines);

        return $string;
    }
}
