<?php declare(strict_types=1);

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Parse;

/**
 * A token from a unified diff file.
 */
final class Token
{
    public const ORIGINAL_FILENAME = 'original_filename';
    public const NEW_FILENAME = 'new_filename';

    public const HUNK_ORIGINAL_START = 'hunk_original_start';
    public const HUNK_ORIGINAL_COUNT = 'hunk_original_count';
    public const HUNK_ORIGINAL_ONE_LINE = 'hunk_original_one_line';
    public const HUNK_NEW_START = 'hunk_new_start';
    public const HUNK_NEW_COUNT = 'hunk_new_count';
    public const HUNK_NEW_ONE_LINE = 'hunk_new_one_line';

    public const SOURCE_LINE_ADDED = 'source_line_added';
    public const SOURCE_LINE_REMOVED = 'source_line_removed';
    public const SOURCE_LINE_UNCHANGED = 'source_line_unchanged';
    public const SOURCE_NO_NEWLINE_EOF = 'source_no_newline_eof';


    /** @var string One of class constants */
    private $type;

    /** @var string The raw value. */
    private $value;

    /** @var string */
    private $lineDelimiter;


    public function __construct(string $type, string $value, string $lineDelimiter)
    {
        $this->type = $type;
        $this->value = $value;
        $this->lineDelimiter = $lineDelimiter;
    }

    /**
     * The token type, should be one of the class constants.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the raw value.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Get the line delimiter (if present)
     */
    public function getLineDelimiter(): string
    {
        return $this->lineDelimiter;
    }
}
