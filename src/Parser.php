<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2017 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser;

use ptlis\DiffParser\Parse\DiffNormalizerInterface;
use ptlis\DiffParser\Parse\GitDiffNormalizer;
use ptlis\DiffParser\Parse\StandardDiffNormalizer;
use ptlis\DiffParser\Parse\SvnDiffNormalizer;
use ptlis\DiffParser\Parse\UnifiedDiffParser;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;

/**
 * Utility class providing a simple API through which parsing can be performed.
 */
final class Parser
{
    const VCS_GIT = 'git';
    const VCS_SVN = 'svn';


    /**
     * Accepts an array of diff lines & returns a Changeset instance.
     *
     * @param string[] $lines
     * @param string $vcsType
     *
     * @return Changeset
     */
    public function parseLines(array $lines, $vcsType = '')
    {
        $parser = $this->getParser($vcsType);

        return $parser->parse($lines);
    }

    /**
     * Accepts an filename for a diff & returns a Changeset instance.
     *
     * @param string $filename
     * @param string $vcsType
     *
     * @return Changeset
     */
    public function parseFile($filename, $vcsType = '')
    {
        $parser = $this->getParser($vcsType);

        if (!file_exists($filename)) {
            throw new \RuntimeException(
                'File "' . $filename . '" not found.'
            );
        }

        return $parser->parse(
            file($filename, FILE_IGNORE_NEW_LINES)
        );
    }

    /**
     * Accepts the VCS type (if present) and returns a parser.
     *
     * @param string $vcsType
     *
     * @return UnifiedDiffParser
     */
    private function getParser($vcsType)
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
     * @param string $vcsType
     *
     * @return DiffNormalizerInterface
     */
    private function getNormalizer($vcsType)
    {
        if (self::VCS_GIT === $vcsType) {
            $normalizer = new GitDiffNormalizer();

        } elseif (self::VCS_SVN === $vcsType) {
            $normalizer = new SvnDiffNormalizer();

        } else {
            $normalizer = new StandardDiffNormalizer();
        }

        return $normalizer;
    }
}
