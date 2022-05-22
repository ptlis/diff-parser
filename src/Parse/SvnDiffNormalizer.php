<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Parse;

/**
 * Normalize git-specific behaviours when unified diff is generated.
 */
final class SvnDiffNormalizer implements DiffNormalizerInterface
{
    public const FILENAME_REGEX = '/^
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
     */
    public function getFilename(RawDiffLine $fileStartLine): string
    {
        // In case of parse error fall back to returning the line minus the plus or minus symbols.
        if (!\preg_match(self::FILENAME_REGEX, $fileStartLine->content, $matches)) {
            return \substr($fileStartLine->content, 4);
        }

        return $matches['filename'];
    }
}
