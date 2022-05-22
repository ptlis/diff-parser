<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser;

/**
 * Class storing data about changed files.
 *
 * @phpstan-type FileOperation File::CREATED|File::DELETED|File::CHANGED
 */
final class File
{
    public const CREATED = 'created';
    public const DELETED = 'deleted';
    public const CHANGED = 'changed';

    /**
     * @param string $originalFilename The original filename.
     * @param string $newFilename The new filename.
     * @param string $operation The nature of the operation, one of class constants.
     * @param array<Hunk> $hunks Array of hunks.
     * @phpstan-param FileOperation $operation
     */
    public function __construct(
        public readonly string $originalFilename,
        public readonly string $newFilename,
        public readonly string $operation,
        public readonly array $hunks
    ) {
    }

    /**
     * Get the original name of the file.
     *
     * @deprecated This data should be accessed via the $originalFilename property.
     */
    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    /**
     * Get the new name of the file.
     *
     * @deprecated This data should be accessed via the $newFilename property.
     */
    public function getNewFilename(): string
    {
        return $this->newFilename;
    }

    /**
     * Get the operation performed on the file (one of class constants).
     *
     * @deprecated This data should be accessed via the $operation property.
     *
     * @phpstan-return FileOperation
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * Get an array of hunks for this file.
     *
     * @deprecated This data should be accessed via the $hunkList property.
     *
     * @return array<Hunk>
     */
    public function getHunks(): array
    {
        return $this->hunks;
    }

    /**
     * Get the string representation of the changed file.
     */
    public function __toString(): string
    {
        $filenames = \implode(
            '',
            [
                '--- ',
                $this->originalFilename,
                PHP_EOL,
                '+++ ',
                $this->newFilename,
                PHP_EOL
            ]
        );

        return $filenames . \implode('', $this->hunks);
    }
}
