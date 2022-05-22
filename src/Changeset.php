<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser;

/**
 * Class storing data for a single changeset.
 */
final class Changeset
{
    /**
     * @param array<File> $files Array of changed files in this diff.
     */
    public function __construct(
        public readonly array $files
    ) {
    }

    /**
     * Get an array of changed files.
     *
     * @deprecated This data should be accessed via the $changedFileList property.
     * @return array<File>
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Get the string representation of the diff.
     */
    public function __toString(): string
    {
        return \implode(PHP_EOL, $this->files);
    }
}
