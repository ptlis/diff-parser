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
    const ORIGINAL_FILENAME = 'original_filename';
    const NEW_FILENAME = 'new_filename';

    const FILE_DELETION_LINE_COUNT = 'file_deletion';
    const FILE_CREATION_LINE_COUNT = 'file_creation';

    const HUNK_ORIGINAL_START = 'hunk_original_start';
    const HUNK_ORIGINAL_COUNT = 'hunk_original_count';
    const HUNK_NEW_START = 'hunk_new_start';
    const HUNK_NEW_COUNT = 'hunk_new_count';

    const SOURCE_LINE_ADDED = 'source_line_added';
    const SOURCE_LINE_REMOVED = 'source_line_removed';
    const SOURCE_LINE_UNCHANGED = 'source_line_unchanged';


    /** @var string  */
    private $type;

    /** @var string The raw value. */
    private $value;


    public function __construct(string $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;
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
}
