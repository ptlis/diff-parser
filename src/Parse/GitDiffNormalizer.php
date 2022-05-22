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
final class GitDiffNormalizer implements DiffNormalizerInterface
{
    /**
     * Accepts a raw file start line from a unified diff & returns a normalized version of the filename.
     */
    public function getFilename(RawDiffLine $fileStartLine): string
    {
        // Strip '+++ ' or '--- '
        $normalized = \substr($fileStartLine->content, 4);

        // Extract first two characters so that we can check for 'a/' or 'b/' prefix
        $prefix = \substr($normalized, 0, 2);

        if (\in_array($prefix, ['a/', 'b/'])) {
            return \substr($normalized, 2);
        } else {
            return $normalized;
        }
    }
}
