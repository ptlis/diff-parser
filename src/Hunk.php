<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2015 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser;

/**
 * Class storing data about hunks in changed files.
 */
class Hunk
{
    /** @var int The original starting line. */
    private $originalStart;

    /** @var int The original line count. */
    private $originalCount;

    /** @var int The new starting line. */
    private $newStart;

    /** @var int The new line count. */
    private $newCount;

    /** @var Line[] The List of lines in this hunk. */
    private $lineList;


    /**
     * Constructor.
     *
     * @param int $originalStart
     * @param int $originalCount
     * @param int $newStart
     * @param int $newCount
     * @param Line[] $lineList
     */
    public function __construct($originalStart, $originalCount, $newStart, $newCount, array $lineList)
    {
        $this->originalStart = $originalStart;
        $this->originalCount = $originalCount;
        $this->newStart = $newStart;
        $this->newCount = $newCount;
        $this->lineList = $lineList;
    }

    /**
     * Get the original starting line.
     *
     * @return int
     */
    public function getOriginalStart()
    {
        return $this->originalStart;
    }

    /**
     * Get the original number of lines.
     *
     * @return int
     */
    public function getOriginalCount()
    {
        return $this->originalCount;
    }

    /**
     * Get het new Start line.
     *
     * @return int
     */
    public function getNewStart()
    {
        return $this->newStart;
    }

    /**
     * Get the new number of lines.
     *
     * @return int
     */
    public function getNewCount()
    {
        return $this->newCount;
    }

    /**
     * Get the lines for this hunk.
     *
     * @return Line[]
     */
    public function getLines()
    {
        return $this->lineList;
    }

    /**
     * Get the string representation of this hunk.
     *
     * @return string
     */
    public function __toString()
    {
        $string = implode(
            '',
            array(
                '@@ -',
                $this->originalStart,
                ',',
                $this->originalCount,
                ' +',
                $this->newStart,
                ',',
                $this->newCount,
                ' @@',
                PHP_EOL
            )
        );

        $string .= implode(PHP_EOL, $this->lineList);

        return $string;
    }
}
