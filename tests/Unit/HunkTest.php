<?php declare(strict_types=1);

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\DiffParser\Test\Unit;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;

/**
 * @covers \ptlis\DiffParser\Hunk
 */
final class HunkTest extends TestCase
{
    /** @var Hunk */
    private $hunk;

    /** @var Hunk */
    private $hunkNoNewlineEof;


    protected function setUp(): void
    {
        $this->hunk = $this->buildHunk(true);
        $this->hunkNoNewlineEof = $this->buildHunk(false);
    }

    private function buildHunk(bool $hasNewlineEof): Hunk
    {
        return new Hunk(
            3,
            7,
            4,
            6,
            PHP_EOL,
            [
                new Line(
                    3,
                    4,
                    Line::UNCHANGED,
                    'A simple VCS wrapper for PHP attempting to offer a consistent API across VCS tools.',
                    PHP_EOL
                ),
                new Line(
                    4,
                    5,
                    Line::UNCHANGED,
                    '',
                    PHP_EOL
                ),
                new Line(
                    5,
                    6,
                    Line::UNCHANGED,
                    '',
                    PHP_EOL
                ),
                new Line(
                    6,
                    -1,
                    Line::REMOVED,
                    '[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)](https://travis-ci.org/ptlis/vcs) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/vcs/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/vcs/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Latest Stable Version](https://poser.pugx.org/ptlis/vcs/v/stable.png)](https://packagist.org/packages/ptlis/vcs)',
                    PHP_EOL
                ),
                new Line(
                    -1,
                    7,
                    Line::ADDED,
                    '[![Build Status](https://travis-ci.org/ptlis/vcs.png?branch=master)](https://travis-ci.org/ptlis/vcs) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/vcs/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/vcs/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Latest Stable Version](https://poser.pugx.org/ptlis/vcs/v/stable.png)](https://packagist.org/packages/ptlis/vcs)',
                    PHP_EOL
                ),
                new Line(
                    7,
                    -1,
                    Line::REMOVED,
                    '',
                    PHP_EOL
                ),
                new Line(
                    8,
                    8,
                    Line::UNCHANGED,
                    '',
                    PHP_EOL
                ),
                new Line(
                    9,
                    9,
                    Line::UNCHANGED,
                    '## Cautions',
                    $hasNewlineEof ? PHP_EOL : ''
                )
            ]
        );
    }

    public function testHunk(): void
    {
        $hunkString = implode(
            PHP_EOL,
            [
                '@@ -3,7 +4,6 @@',
                ' A simple VCS wrapper for PHP attempting to offer a consistent API across VCS tools.',
                ' ',
                ' ',
                '-[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)](https://travis-ci.org/ptlis/vcs) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/vcs/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/vcs/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Latest Stable Version](https://poser.pugx.org/ptlis/vcs/v/stable.png)](https://packagist.org/packages/ptlis/vcs)',
                '+[![Build Status](https://travis-ci.org/ptlis/vcs.png?branch=master)](https://travis-ci.org/ptlis/vcs) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/vcs/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/vcs/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Latest Stable Version](https://poser.pugx.org/ptlis/vcs/v/stable.png)](https://packagist.org/packages/ptlis/vcs)',
                '-',
                ' ',
                ' ## Cautions',
                ''
            ]
        );

        $this->assertEquals($hunkString, $this->hunk->__toString());
        $this->assertEquals(3, $this->hunk->getOriginalStart());
        $this->assertEquals(7, $this->hunk->getOriginalCount());
        $this->assertEquals(4, $this->hunk->getNewStart());
        $this->assertEquals(6, $this->hunk->getNewCount());
    }

    public function testHunkNoNewlineEof(): void
    {
        $hunkString = implode(
            PHP_EOL,
            [
                '@@ -3,7 +4,6 @@',
                ' A simple VCS wrapper for PHP attempting to offer a consistent API across VCS tools.',
                ' ',
                ' ',
                '-[![Build Status](https://travis-ci.org/ptlis/conneg.png?branch=master)](https://travis-ci.org/ptlis/vcs) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/vcs/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/vcs/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Latest Stable Version](https://poser.pugx.org/ptlis/vcs/v/stable.png)](https://packagist.org/packages/ptlis/vcs)',
                '+[![Build Status](https://travis-ci.org/ptlis/vcs.png?branch=master)](https://travis-ci.org/ptlis/vcs) [![Code Coverage](https://scrutinizer-ci.com/g/ptlis/vcs/badges/coverage.png?s=6c30a32e78672ae0d7cff3ecf00ceba95049879a)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ptlis/vcs/badges/quality-score.png?s=b8a262b33dd4a5de02d6f92f3e318ebb319f96c0)](https://scrutinizer-ci.com/g/ptlis/vcs/) [![Latest Stable Version](https://poser.pugx.org/ptlis/vcs/v/stable.png)](https://packagist.org/packages/ptlis/vcs)',
                '-',
                ' ',
                ' ## Cautions',
                '\ No newline at end of file',
                ''
            ]
        );

        $this->assertEquals($hunkString, $this->hunkNoNewlineEof->__toString());
        $this->assertEquals(3, $this->hunkNoNewlineEof->getOriginalStart());
        $this->assertEquals(7, $this->hunkNoNewlineEof->getOriginalCount());
        $this->assertEquals(4, $this->hunkNoNewlineEof->getNewStart());
        $this->assertEquals(6, $this->hunkNoNewlineEof->getNewCount());
    }
}
