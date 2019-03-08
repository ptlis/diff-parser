<?php declare(strict_types=1);

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser;

/**
 * Class storing data about changed files.
 */
final class File
{
    const CREATED = 'created';
    const DELETED = 'deleted';
    const CHANGED = 'changed';

    /** @var string The original filename. */
    private $originalFilename;

    /** @var string The new filename. */
    private $newFilename;

    /** @var string The file operation, one of class constants. */
    private $operation;

    /** @var Hunk[] Array of hunks. */
    private $hunkList;


    /**
     * @param string $originalFilename
     * @param string $newFilename
     * @param string $operation One of class constants.
     * @param Hunk[] $hunkList
     */
    public function __construct(string $originalFilename, string $newFilename, string $operation, array $hunkList)
    {
        $this->originalFilename = $originalFilename;
        $this->newFilename = $newFilename;
        $this->operation = $operation;
        $this->hunkList = $hunkList;
    }

    /**
     * Get the original name of the file.
     */
    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    /**
     * Get the new name of the file.
     */
    public function getNewFilename(): string
    {
        return $this->newFilename;
    }

    /**
     * Get the operation performed on the file (one of class constants).
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * Get an array of hunks for this file.
     *
     * @return Hunk[]
     */
    public function getHunks(): array
    {
        return $this->hunkList;
    }

    /**
     * Get the string representation of the changed file.
     */
    public function __toString(): string
    {
        $filenames = implode(
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

        return $filenames . implode('', $this->hunkList);
    }
}
