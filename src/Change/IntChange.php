<?php

declare(strict_types=1);

namespace ptlis\DiffParser\Change;

/**
 * Represents an integer value that is changed between the original & new file.
 */
final class IntChange
{
    public function __construct(
        public readonly int $original,
        public readonly int $new
    ) {
    }
}
