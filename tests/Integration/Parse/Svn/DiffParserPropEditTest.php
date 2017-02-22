<?php

/**
 * PHP Version 5.3
 *
 * @copyright (c) 2014-2017 brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Test\Integration\Parse\Svn;

use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Parse\UnifiedDiffParser;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;
use ptlis\DiffParser\Parse\SvnDiffNormalizer;

class DiffParserPropEditTest extends \PHPUnit_Framework_TestCase
{
    public function testPropEdit()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff_propedit', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);

        $this->assertInstanceOf('ptlis\DiffParser\Changeset', $diff);
        $this->assertEquals(0, count($diff->getFiles()));
    }
}
