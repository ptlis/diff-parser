<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2017 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Parse;

/**
 * Normalize generic diff (no specific behaviours expected).
 */
final class StandardDiffNormalizer implements DiffNormalizerInterface
{
    /**
     * Accepts a raw file start line from a unified diff & returns a normalized version of the filename.
     *
     * @param string $fileStartLine
     *
     * @return string
     */
    public function getFilename($fileStartLine)
    {
        return substr($fileStartLine, 4);
    }
}
