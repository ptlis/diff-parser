<?php declare(strict_types=1);

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser;

use ptlis\DiffParser\Parse\DiffNormalizerInterface;
use ptlis\DiffParser\Parse\GitDiffNormalizer;
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
    public function parseLines(array $lines, string $vcsType = ''): Changeset
    {
        $parser = $this->getParser($vcsType);

        return $parser->parse($lines);
    }

    /**
     * Accepts an filename for a diff & returns a Changeset instance.
     */
    public function parseFile(string $filename, string $vcsType = ''): Changeset
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
