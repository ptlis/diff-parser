<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2017 brian ridley
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
     * Constructor.
     *
     * @param string $originalFilename
     * @param string $newFilename
     * @param string $operation One of class constants.
     * @param Hunk[] $hunkList
     */
    public function __construct($originalFilename, $newFilename, $operation, array $hunkList)
    {
        $this->originalFilename = $originalFilename;
        $this->newFilename = $newFilename;
        $this->operation = $operation;
        $this->hunkList = $hunkList;
    }

    /**
     * Get the original name of the file.
     *
     * @return string
     */
    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }

    /**
     * Get the new name of the file.
     *
     * @return string
     */
    public function getNewFilename()
    {
        return $this->newFilename;
    }

    /**
     * Get the operation performed on the file (one of class constants).
     *
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Get an array of hunks for this file.
     *
     * @return Hunk[]
     */
    public function getHunks()
    {
        return $this->hunkList;
    }

    /**
     * Get the string representation of the changed file.
     *
     * @return string
     */
    public function __toString()
    {
        $filenames = implode(
            '',
            array(
                '--- ',
                $this->originalFilename,
                PHP_EOL,
                '+++ ',
                $this->newFilename,
                PHP_EOL
            )
        );

        return $filenames . implode('', $this->hunkList);
    }
}
