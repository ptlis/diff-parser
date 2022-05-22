<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser;

use ptlis\DiffParser\Parse\DiffNormalizerInterface;
use ptlis\DiffParser\Parse\GitDiffNormalizer;
use ptlis\DiffParser\Parse\SvnDiffNormalizer;
use ptlis\DiffParser\Parse\UnifiedDiffParser;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;

/**
 * Utility class providing a simple API through which parsing can be performed.
 *
 * @phpstan-type VcsType Parser::VCS_GIT|Parser::VCS_SVN|""
 */
final class Parser
{
    public const VCS_GIT = 'git';
    public const VCS_SVN = 'svn';

    /**
     * @phpstan-param VcsType $vcsType
     */
    public function parse(string $patchFile, string $vcsType = ''): Changeset
    {
        $parser = $this->getParser($vcsType);

        return $parser->parse($patchFile);
    }

    /**
     * Accepts a path to a patch file & returns a Changeset instance.
     *
     * @phpstan-param VcsType $vcsType
     */
    public function parseFile(string $filename, string $vcsType = ''): Changeset
    {
        try {
            $fileContents = (string)\file_get_contents($filename);
        } catch (\Throwable) {
            throw new \RuntimeException(
                'File "' . $filename . '" not found.'
            );
        }
        return $this->parse($fileContents, $vcsType);
    }

    /**
     * Accepts the VCS type (if present) and returns a parser.
     *
     * @phpstan-param VcsType $vcsType
     */
    private function getParser(string $vcsType): UnifiedDiffParser
    {
        return new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                $this->getNormalizer($vcsType)
            )
        );
    }

    /**
     * Returns an appropriate normalizer for the VCS type.
     *
     * @phpstan-param VcsType $vcsType
     */
    private function getNormalizer(string $vcsType): DiffNormalizerInterface
    {
        $normalizer = new GitDiffNormalizer();
        if (self::VCS_SVN === $vcsType) {
            $normalizer = new SvnDiffNormalizer();
        }
        return $normalizer;
    }
}
