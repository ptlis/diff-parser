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
 * Normalize git-specific behaviours when unified diff is generated.
 */
final class SvnDiffNormalizer implements DiffNormalizerInterface
{
    const FILENAME_REGEX = '/^
            (?:(?:\+){3}|(?:\-){3}) # +++ or ---
            \s
            (?<filename>.*)         # the filename
            \s
            (:?\(revision\s(:?\d+)\))    # the revision number
            |
            (:?\(nonexistent\))     # or nonexistent
            \s*$
        /x';


    /**
     * Accepts a raw file start line from a unified diff & returns a normalized version of the filename.
     *
     * @param string $fileStartLine
     *
     * @return string
     */
    public function getFilename($fileStartLine)
    {
        // In case of parse error fall back to returning the line minus the plus or minus symbols.
        if (!preg_match(static::FILENAME_REGEX, $fileStartLine, $matches)) {
            return substr($fileStartLine, 4);
        }

        return $matches['filename'];
    }
}
