<?php

/**
 * @copyright (c) 2014-present brian ridley
 * @author brian ridley <ptlis@ptlis.net>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace ptlis\DiffParser\Test\Integration\Parse\Svn;

use PHPUnit\Framework\TestCase;
use ptlis\DiffParser\Change\IntChange;
use ptlis\DiffParser\Change\StringChange;
use ptlis\DiffParser\Changeset;
use ptlis\DiffParser\File;
use ptlis\DiffParser\Hunk;
use ptlis\DiffParser\Line;
use ptlis\DiffParser\Parse\UnifiedDiffParser;
use ptlis\DiffParser\Parse\UnifiedDiffTokenizer;
use ptlis\DiffParser\Parse\SvnDiffNormalizer;

/**
 * @coversNothing
 */
final class DiffParserAddTest extends TestCase
{
    public function testParseCount(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_add_single_line');

        $diff = $parser->parse($data);

        $this->assertInstanceOf(Changeset::class, $diff);
        $this->assertCount(1, $diff->files);
    }

    public function testFileAddSingleLinePre19(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_add_single_line');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(1, $fileList[0]->hunks);

        $file = new File(
            new StringChange('README.md', 'README.md'),
            File::CREATED,
            [
                new Hunk(
                    new IntChange(0, 1),
                    new IntChange(0, 1),
                    "\n",
                    [
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 1), Line::ADDED, '## Test', '')
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testFileAddPost19(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_add_1.9');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(1, $fileList[0]->hunks);

        $file = new File(
            new StringChange('', 'foo'),
            File::CREATED,
            [
                new Hunk(
                    new IntChange(0, 1),
                    new IntChange(0, 3),
                    "\n",
                    [
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 1), Line::ADDED, '<?php', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 2), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 3), Line::ADDED, 'echo \'test\';', "\n")
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testFileMultiLine(): void
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = (string)\file_get_contents(__DIR__ . '/data/diff_add_multi_line');

        $diff = $parser->parse($data);
        $fileList = $diff->files;

        $this->assertCount(1, $fileList[0]->hunks);

        $file = new File(
            new StringChange(
                'modules/dPcompteRendu/controllers/do_add_doc_object.php',
                'modules/dPcompteRendu/controllers/do_add_doc_object.php'
            ),
            File::CREATED,
            [
                new Hunk(
                    new IntChange(0, 1),
                    new IntChange(0, 74),
                    "\n",
                    [
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 1), Line::ADDED, '<?php', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 2), Line::ADDED, '/**', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 3), Line::ADDED, ' * @package Mediboard\CompteRendu', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 4), Line::ADDED, ' * @author  SAS OpenXtrem <dev@openxtrem.com>', "\n"),
                        new Line(
                            new IntChange(Line::LINE_NOT_PRESENT, 5),
                            Line::ADDED,
                            ' * @license https://www.gnu.org/licenses/gpl.html GNU General Public License',
                            "\n"
                        ),
                        new Line(
                            new IntChange(Line::LINE_NOT_PRESENT, 6),
                            Line::ADDED,
                            ' * @license https://www.openxtrem.com/licenses/oxol.html OXOL OpenXtrem Open License',
                            "\n"
                        ),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 7), Line::ADDED, ' */', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 8), Line::ADDED, '', "\n"),
                        new Line(
                            new IntChange(Line::LINE_NOT_PRESENT, 9),
                            Line::ADDED,
                            '$compte_rendu_id = CView::post("compte_rendu_id", "ref class|CCompteRendu");',
                            "\n"
                        ),
                        new Line(
                            new IntChange(Line::LINE_NOT_PRESENT, 10),
                            Line::ADDED,
                            '$pack_id         = CView::post("pack_id", "ref class|CPack");',
                            "\n"
                        ),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 11), Line::ADDED, '$object_class    = CView::post("object_class", "str");', "\n"),
                        new Line(
                            new IntChange(Line::LINE_NOT_PRESENT, 12),
                            Line::ADDED,
                            '$object_id       = CView::post("object_id", "ref class|$object_class");',
                            "\n"
                        ),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 13), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 14), Line::ADDED, 'CView::checkin();', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 15), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 16), Line::ADDED, '$compte_rendu = new CCompteRendu();', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 17), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 18), Line::ADDED, '$header_id = $footer_id = null;', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 19), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 20), Line::ADDED, 'if ($pack_id) {', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 21), Line::ADDED, '  $pack = new CPack();', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 22), Line::ADDED, '  $pack->load($pack_id);', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 23), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 24), Line::ADDED, '  $compte_rendu->loadContent();', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 25), Line::ADDED, '  $pack->loadContent();', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 26), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 27), Line::ADDED, '  $compte_rendu->nom = $pack->nom;', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 28), Line::ADDED, '  $compte_rendu->_source = $pack->_source;', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 29), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 30), Line::ADDED, '  $pack->loadHeaderFooter();', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 31), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 32), Line::ADDED, '  $header_id = $pack->_header_found->_id;', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 33), Line::ADDED, '  $footer_id = $pack->_footer_found->_id;', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 34), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 35), Line::ADDED, '  // Marges et format', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 36), Line::ADDED, '  /** @var $links CModeleToPack[] */', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 37), Line::ADDED, '  $links = $pack->_back[\'modele_links\'];', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 38), Line::ADDED, '  $first_modele = reset($links);', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 39), Line::ADDED, '  $first_modele = $first_modele->_ref_modele;', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 40), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 41), Line::ADDED, '  foreach (array(', "\n"),
                        new Line(
                            new IntChange(Line::LINE_NOT_PRESENT, 42),
                            Line::ADDED,
                            '    "factory", "font", "size", "page_height", "page_width",',
                            "\n"
                        ),
                        new Line(
                            new IntChange(Line::LINE_NOT_PRESENT, 43),
                            Line::ADDED,
                            '    "margin_top", "margin_left", "margin_right", "margin_bottom"',
                            "\n"
                        ),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 44), Line::ADDED, '    ) as $_field) {', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 45), Line::ADDED, '    $compte_rendu->{$_field} = $first_modele->{$_field};', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 46), Line::ADDED, '  }', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 47), Line::ADDED, '}', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 48), Line::ADDED, 'else {', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 49), Line::ADDED, '  $compte_rendu->load($compte_rendu_id);', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 50), Line::ADDED, '  $compte_rendu->loadContent();', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 51), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 52), Line::ADDED, '  $compte_rendu->_id = "";', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 53), Line::ADDED, '}', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 54), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 55), Line::ADDED, '$compte_rendu->object_class = $object_class;', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 56), Line::ADDED, '$compte_rendu->object_id = $object_id;', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 57), Line::ADDED, '$compte_rendu->user_id = "";', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 58), Line::ADDED, '$compte_rendu->function_id = "";', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 59), Line::ADDED, '$compte_rendu->group_id = "";', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 60), Line::ADDED, '$compte_rendu->content_id = "";', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 61), Line::ADDED, '$compte_rendu->_ref_content->_id = "";', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 62), Line::ADDED, '', "\n"),
                        new Line(
                            new IntChange(Line::LINE_NOT_PRESENT, 63),
                            Line::ADDED,
                            '$compte_rendu->_source = $compte_rendu->generateDocFromModel(null, $header_id, $footer_id'
                            . ');',
                            "\n"
                        ),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 64), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 65), Line::ADDED, '$msg = $compte_rendu->store();', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 66), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 67), Line::ADDED, 'if ($msg) {', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 68), Line::ADDED, '  CAppUI::setMsg($msg, UI_MSG_ERROR);', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 69), Line::ADDED, '}', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 70), Line::ADDED, 'else {', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 71), Line::ADDED, '  CAppUI::setMsg(CAppUI::tr("CCompteRendu-msg-create"));', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 72), Line::ADDED, '}', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 73), Line::ADDED, '', "\n"),
                        new Line(new IntChange(Line::LINE_NOT_PRESENT, 74), Line::ADDED, 'echo CAppUI::getMsg();', ''),
                    ]
                )
            ]
        );

        $this->assertEquals($file, $fileList[0]);
    }
}
