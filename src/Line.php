<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2017 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser;

/**
 * Class storing metadata about a single line from a hunk.
 */
final class Line
{
    const ADDED = 'added';
    const REMOVED = 'removed';
    const UNCHANGED = 'unchanged';

    const LINE_NOT_PRESENT = -1;

    /** @var int The original line number (before change applied), -1 if not present. */
    private $originalLineNo;

    /** @var int The new line number (after change applied), -1 if not present. */
    private $newLineNo;

    /** @var string The operation performed on this line (one of class constants). */
    private $operation;

    /** @var string The value of this line. */
    private $content;


    /**
     * Constructor.
     *
     * @param int $originalLineNo
     * @param int $newLineNo
     * @param string $operation
     * @param string $content
     */
    public function __construct($originalLineNo, $newLineNo, $operation, $content)
    {
        $this->originalLineNo = $originalLineNo;
        $this->newLineNo = $newLineNo;
        $this->operation = $operation;
        $this->content = $content;
    }

    /**
     * Get the original line number (before change applied), -1 if not present.
     *
     * @return int
     */
    public function getOriginalLineNo()
    {
        return intval($this->originalLineNo);
    }

    /**
     * Get the new line number (after change applied), -1 if not present.
     *
     * @return int
     */
    public function getNewLineNo()
    {
        return intval($this->newLineNo);
    }

    /**
     * Get the operation performed (one of class constants).
     *
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Get the content of the line.
     *
     * @return string
     */
    public function getContent()
    {
        return strval($this->content);
    }

    /**
     * Get the string representation of this line.
     *
     * @return string
     */
    public function __toString()
    {
        switch ($this->operation) {
            case self::ADDED:
                $string = '+';
                break;
            case self::REMOVED:
                $string = '-';
                break;
            default:
                $string = ' ';
                break;
        }

        $string .= $this->content;

        return $string;
    }
}
