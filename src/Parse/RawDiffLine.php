<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Parse;

/**
 * Represents a single line from a diff split by contents & terminator (\n, \r\n or \r).
 */
final class RawDiffLine
{
    public function __construct(
        public readonly string $content,
        public readonly string $lineTerminator
    ) {
    }
}
