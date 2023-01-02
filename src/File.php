<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser;

use ptlis\DiffParser\Change\StringChange;

/**
 * Class storing data about changed files.
 *
 * @phpstan-type FileOperation File::CREATED|File::DELETED|File::CHANGED
 */
final class File
{
    use TriggerDeprecationTrait;

    public const CREATED = 'created';
    public const DELETED = 'deleted';
    public const CHANGED = 'changed';

    /** @deprecated This data should be accessed via the $filename->original property.*/
    public readonly string $originalFilename;
    /** @deprecated This data should be accessed via the $filename->new property.*/
    public readonly string $newFilename;

    /**
     * @param string $operation The nature of the operation, one of class constants.
     * @param array<Hunk> $hunks Array of hunks.
     * @phpstan-param FileOperation $operation
     */
    public function __construct(
        public readonly StringChange $filename,
        public readonly string $operation,
        public readonly array $hunks
    ) {
        $this->originalFilename = $this->filename->original;
        $this->newFilename = $this->filename->new;
    }

    /**
     * Get the original name of the file.
     *
     * @deprecated This data should be accessed via the $filename->original property.
     */
    public function getOriginalFilename(): string
    {
        $this->triggerDeprecationWarning(__METHOD__, 'originalFilename');
        return $this->filename->original;
    }

    /**
     * Get the new name of the file.
     *
     * @deprecated This data should be accessed via the $filename->new property.
     */
    public function getNewFilename(): string
    {
        $this->triggerDeprecationWarning(__METHOD__, 'newFilename');
        return $this->filename->new;
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
        $this->triggerDeprecationWarning(__METHOD__, 'operation');
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
        $this->triggerDeprecationWarning(__METHOD__, 'hunks');
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
