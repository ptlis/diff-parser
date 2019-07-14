<?php declare(strict_types=1);

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser;

/**
 * Class storing data about hunks in changed files.
 */
final class Hunk
{
    /** @var int The original starting line. */
    private $originalStart;

    /** @var int The original line count. */
    private $originalCount;

    /** @var int The new starting line. */
    private $newStart;

    /** @var int The new line count. */
    private $newCount;

    /** @var string Delimiter between the 'meta' line (file offsets) and the hunk data. */
    private $metaLineDelimiter;

    /** @var Line[] The List of lines in this hunk. */
    private $lineList;


    /**
     * Constructor.
     *
     * @param int $originalStart
     * @param int $originalCount
     * @param int $newStart
     * @param int $newCount
     * @param string $metaLineDelimiter
     * @param Line[] $lineList
     */
    public function __construct(
        int $originalStart,
        int $originalCount,
        int $newStart,
        int $newCount,
        string $metaLineDelimiter,
        array $lineList
    ) {
        $this->originalStart = $originalStart;
        $this->originalCount = $originalCount;
        $this->newStart = $newStart;
        $this->newCount = $newCount;
        $this->metaLineDelimiter = $metaLineDelimiter;
        $this->lineList = $lineList;
    }

    /**
     * Get the original starting line.
     */
    public function getOriginalStart(): int
    {
        return intval($this->originalStart);
    }

    /**
     * Get the original number of lines.
     */
    public function getOriginalCount(): int
    {
        return intval($this->originalCount);
    }

    /**
     * Get het new Start line.
     */
    public function getNewStart(): int
    {
        return intval($this->newStart);
    }

    /**
     * Get the new number of lines.
     */
    public function getNewCount(): int
    {
        return intval($this->newCount);
    }

    /**
     * Get the lines for this hunk.
     *
     * @return Line[]
     */
    public function getLines(): array
    {
        return $this->lineList;
    }

    /**
     * Get the string representation of this hunk.
     */
    public function __toString(): string
    {
        $string = implode(
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

        $string .= implode('', $this->lineList);

        return $string;
    }
}
