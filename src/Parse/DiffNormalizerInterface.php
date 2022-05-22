<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Parse;

/**
 * Interface class used with UnifiedDiffTokenizer to normalize VCS-specific behaviours within unified diffs.
 */
interface DiffNormalizerInterface
{
    /**
     * Accepts a raw file start line from a unified diff & returns a normalized version of the filename.
     */
    public function getFilename(RawDiffLine $fileStartLine): string;
}
