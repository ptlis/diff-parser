<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2015 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Parse;

/**
 * Normalize git-specific behaviours when unified diff is generated.
 */
class SvnDiffNormalizer implements DiffNormalizerInterface
{
    const FILENAME_REGEX = '/^
            (?:(?:\+){3}|(?:\-){3}) # +++ or ---
            \s
            (?<filename>.*)         # the filename
            \s
            \(revision\s\d\)\s*$    # the revision number
        /x';


    /**
     * Accepts a raw file start line from a unified diff & returns a normalized version of the filename.
     *
     * @throws \RuntimeException
     *
     * @param string $fileStartLine
     *
     * @return string
     */
    public function getFilename($fileStartLine)
    {
        if (!preg_match(static::FILENAME_REGEX, $fileStartLine, $matches)) {
            throw new \RuntimeException('Invalid diff file definition.');
        }

        return $matches['filename'];
    }
}
