<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Parse;

/**
 * A token from a unified diff file.
 *
 * @phpstan-type TokenType Token::ORIGINAL_FILENAME|Token::NEW_FILENAME|Token::HUNK_ORIGINAL_START|Token::HUNK_ORIGINAL_COUNT|Token::HUNK_ORIGINAL_ONE_LINE|Token::HUNK_NEW_START|Token::HUNK_NEW_COUNT|Token::HUNK_NEW_ONE_LINE|Token::SOURCE_LINE_ADDED|Token::SOURCE_LINE_REMOVED|Token::SOURCE_LINE_UNCHANGED|Token::SOURCE_NO_NEWLINE_EOF
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

    /**
     * @param string $type The token type, one of class constants.
     * @param string $value The raw token value.
     * @param string $lineTerminator
     * @phpstan-param TokenType $type
     */
    public function __construct(
        public readonly string $type,
        public readonly string $value,
        public readonly string $lineTerminator
    ) {
    }
}
