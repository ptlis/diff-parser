<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Parse;

final class HunkMetaData
{
    public function __construct(
        public readonly int $originalStart,
        public readonly int $originalCount,
        public readonly int $newStart,
        public readonly int $newCount,
        public readonly int $tokensReadCount,
        public readonly string $lineTerminator
    ) {
    }
}
