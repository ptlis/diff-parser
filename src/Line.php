<?php declare(strict_types=1);

/**
 * @copyright (c) 2014-present brian ridley
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


    public function __construct(int $originalLineNo, int $newLineNo, string $operation, string $content)
    {
        $this->originalLineNo = $originalLineNo;
        $this->newLineNo = $newLineNo;
        $this->operation = $operation;
        $this->content = $content;
    }

    /**
     * Get the original line number (before change applied), -1 if not present.
     */
    public function getOriginalLineNo(): int
    {
        return intval($this->originalLineNo);
    }

    /**
     * Get the new line number (after change applied), -1 if not present.
     */
    public function getNewLineNo(): int
    {
        return intval($this->newLineNo);
    }

    /**
     * Get the operation performed (one of class constants).
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * Get the content of the line.
     */
    public function getContent(): string
    {
        return strval($this->content);
    }

    /**
     * Get the string representation of this line.
     */
    public function __toString(): string
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
