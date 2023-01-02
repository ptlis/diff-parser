<?php

declare(strict_types=1);

namespace ptlis\DiffParser\Change;

/**
 * Represents an string value that is changed between the original & new file.
 */
class StringChange
{
    public function __construct(
        public readonly string $original,
        public readonly string $new
    ) {
    }
}
