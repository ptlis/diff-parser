<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser;

use ptlis\DiffParser\Change\IntChange;

/**
 * Class storing data about hunks in changed files.
 */
final class Hunk
{
    use TriggerDeprecationTrait;

    /** @deprecated This data should be accessed via the $startLine->original */
    public readonly int $originalStart;
    /** @deprecated This data should be accessed via the $affectedLines->original */
    public readonly int $originalCount;
    /** @deprecated This data should be accessed via the $startLine->new */
    public readonly int $newStart;
    /** @deprecated This data should be accessed via the $affectedLines->new */
    public readonly int $newCount;

    /**
     * @param IntChange $startLine The start line for the hunk (original & new).
     * @param IntChange $affectedLines The number of affected lines (original & new).
     * @param string $metaLineDelimiter Delimiter between the 'meta' line (file offsets) and the hunk data.
     * @param array<Line> $lines Array of lines in this hunk.
     */
    public function __construct(
        public readonly IntChange $startLine,
        public readonly IntChange $affectedLines,
        public readonly string $metaLineDelimiter,
        public readonly array $lines
    ) {
        $this->originalStart = $this->startLine->original;
        $this->originalCount = $this->affectedLines->original;
        $this->newStart = $this->startLine->new;
        $this->newCount = $this->affectedLines->new;
    }

    /**
     * Get the original starting line.
     *
     * @deprecated This data should be accessed via the $startLine->original property.
     */
    public function getOriginalStart(): int
    {
        $this->triggerDeprecationWarning(__METHOD__, 'originalStart');
        return $this->startLine->original;
    }

    /**
     * Get the original number of lines.
     *
     * @deprecated This data should be accessed via the $affectedLines->original property.
     */
    public function getOriginalCount(): int
    {
        $this->triggerDeprecationWarning(__METHOD__, 'originalCount');
        return $this->affectedLines->original;
    }

    /**
     * Get het new Start line.
     *
     * @deprecated This data should be accessed via the $startLine->new property.
     */
    public function getNewStart(): int
    {
        $this->triggerDeprecationWarning(__METHOD__, 'newStart');
        return $this->startLine->new;
    }

    /**
     * Get the new number of lines.
     *
     * @deprecated This data should be accessed via the $affectedLines->new property.
     */
    public function getNewCount(): int
    {
        $this->triggerDeprecationWarning(__METHOD__, 'newCount');
        return $this->affectedLines->new;
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
        $this->triggerDeprecationWarning(__METHOD__, 'lines');
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
                $this->startLine->original,
                ',',
                $this->affectedLines->original,
                ' +',
                $this->startLine->new,
                ',',
                $this->affectedLines->new,
                ' @@',
                $this->metaLineDelimiter
            ]
        );

        $string .= \implode('', $this->lines);

        return $string;
    }
}
