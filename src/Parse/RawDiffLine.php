<?php declare(strict_types=1);

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Parse;

/**
 * Represents a single line from a diff split by contents & delimiter (\n, \r\n or \r).
 */
final class RawDiffLine
{
    /** @var string */
    private $content;

    /** @var string */
    private $lineDelimiter;

    public function __construct(
        string $content,
        string $lineDelimiter
    ) {
        $this->content = $content;
        $this->lineDelimiter = $lineDelimiter;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getLineDelimiter(): string
    {
        return $this->lineDelimiter;
    }
}