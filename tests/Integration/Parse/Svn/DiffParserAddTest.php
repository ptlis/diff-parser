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

class DiffParserAddTest extends \PHPUnit_Framework_TestCase
{
    public function testParseCount()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff_add_single_line', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);

        $this->assertInstanceOf('ptlis\DiffParser\Changeset', $diff);
        $this->assertEquals(1, count($diff->getFiles()));
    }

    public function testFileAddSingleLinePre19()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff_add_single_line', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);
        $fileList = $diff->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            '',
            'README.md',
            File::CREATED,
            array(
                new Hunk(
                    0,
                    0,
                    1,
                    1,
                    array(
                        new Line(-1, 1, Line::ADDED, '## Test')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testFileAddPost19()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff_add_1.9', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);
        $fileList = $diff->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            '',
            'foo',
            File::CREATED,
            array(
                new Hunk(
                    0,
                    0,
                    1,
                    3,
                    array(
                        new Line(-1, 1, Line::ADDED, '<?php'),
                        new Line(-1, 2, Line::ADDED, ''),
                        new Line(-1, 3, Line::ADDED, 'echo \'test\';')
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[0]);
    }

    public function testFileMultiLine()
    {
        $parser = new UnifiedDiffParser(
            new UnifiedDiffTokenizer(
                new SvnDiffNormalizer()
            )
        );

        $data = file(__DIR__ . '/data/diff_add_multi_line', FILE_IGNORE_NEW_LINES);

        $diff = $parser->parse($data);
        $fileList = $diff->getFiles();

        $this->assertEquals(1, count($fileList[0]->getHunks()));

        $file = new File(
            'modules/dPcompteRendu/controllers/do_add_doc_object.php',
            'modules/dPcompteRendu/controllers/do_add_doc_object.php',
            File::CREATED,
            array(
                new Hunk(
                    0,
                    0,
                    1,
                    74,
                    array(
                        new Line(-1, 1, Line::ADDED, '<?php'),
                        new Line(-1, 2, Line::ADDED, '/**'),
                        new Line(-1, 3, Line::ADDED, ' * @package Mediboard\CompteRendu'),
                        new Line(-1, 4, Line::ADDED, ' * @author  SAS OpenXtrem <dev@openxtrem.com>'),
                        new Line(-1, 5, Line::ADDED, ' * @license https://www.gnu.org/licenses/gpl.html GNU General Public License'),
                        new Line(-1, 6, Line::ADDED, ' * @license https://www.openxtrem.com/licenses/oxol.html OXOL OpenXtrem Open License'),
                        new Line(-1, 7, Line::ADDED, ' */'),
                        new Line(-1, 8, Line::ADDED, ''),
                        new Line(-1, 9, Line::ADDED, '$compte_rendu_id = CView::post("compte_rendu_id", "ref class|CCompteRendu");'),
                        new Line(-1, 10, Line::ADDED, '$pack_id         = CView::post("pack_id", "ref class|CPack");'),
                        new Line(-1, 11, Line::ADDED, '$object_class    = CView::post("object_class", "str");'),
                        new Line(-1, 12, Line::ADDED, '$object_id       = CView::post("object_id", "ref class|$object_class");'),
                        new Line(-1, 13, Line::ADDED, ''),
                        new Line(-1, 14, Line::ADDED, 'CView::checkin();'),
                        new Line(-1, 15, Line::ADDED, ''),
                        new Line(-1, 16, Line::ADDED, '$compte_rendu = new CCompteRendu();'),
                        new Line(-1, 17, Line::ADDED, ''),
                        new Line(-1, 18, Line::ADDED, '$header_id = $footer_id = null;'),
                        new Line(-1, 19, Line::ADDED, ''),
                        new Line(-1, 20, Line::ADDED, 'if ($pack_id) {'),
                        new Line(-1, 21, Line::ADDED, '  $pack = new CPack();'),
                        new Line(-1, 22, Line::ADDED, '  $pack->load($pack_id);'),
                        new Line(-1, 23, Line::ADDED, ''),
                        new Line(-1, 24, Line::ADDED, '  $compte_rendu->loadContent();'),
                        new Line(-1, 25, Line::ADDED, '  $pack->loadContent();'),
                        new Line(-1, 26, Line::ADDED, ''),
                        new Line(-1, 27, Line::ADDED, '  $compte_rendu->nom = $pack->nom;'),
                        new Line(-1, 28, Line::ADDED, '  $compte_rendu->_source = $pack->_source;'),
                        new Line(-1, 29, Line::ADDED, ''),
                        new Line(-1, 30, Line::ADDED, '  $pack->loadHeaderFooter();'),
                        new Line(-1, 31, Line::ADDED, ''),
                        new Line(-1, 32, Line::ADDED, '  $header_id = $pack->_header_found->_id;'),
                        new Line(-1, 33, Line::ADDED, '  $footer_id = $pack->_footer_found->_id;'),
                        new Line(-1, 34, Line::ADDED, ''),
                        new Line(-1, 35, Line::ADDED, '  // Marges et format'),
                        new Line(-1, 36, Line::ADDED, '  /** @var $links CModeleToPack[] */'),
                        new Line(-1, 37, Line::ADDED, '  $links = $pack->_back[\'modele_links\'];'),
                        new Line(-1, 38, Line::ADDED, '  $first_modele = reset($links);'),
                        new Line(-1, 39, Line::ADDED, '  $first_modele = $first_modele->_ref_modele;'),
                        new Line(-1, 40, Line::ADDED, ''),
                        new Line(-1, 41, Line::ADDED, '  foreach (array('),
                        new Line(-1, 42, Line::ADDED, '    "factory", "font", "size", "page_height", "page_width",'),
                        new Line(-1, 43, Line::ADDED, '    "margin_top", "margin_left", "margin_right", "margin_bottom"'),
                        new Line(-1, 44, Line::ADDED, '    ) as $_field) {'),
                        new Line(-1, 45, Line::ADDED, '    $compte_rendu->{$_field} = $first_modele->{$_field};'),
                        new Line(-1, 46, Line::ADDED, '  }'),
                        new Line(-1, 47, Line::ADDED, '}'),
                        new Line(-1, 48, Line::ADDED, 'else {'),
                        new Line(-1, 49, Line::ADDED, '  $compte_rendu->load($compte_rendu_id);'),
                        new Line(-1, 50, Line::ADDED, '  $compte_rendu->loadContent();'),
                        new Line(-1, 51, Line::ADDED, ''),
                        new Line(-1, 52, Line::ADDED, '  $compte_rendu->_id = "";'),
                        new Line(-1, 53, Line::ADDED, '}'),
                        new Line(-1, 54, Line::ADDED, ''),
                        new Line(-1, 55, Line::ADDED, '$compte_rendu->object_class = $object_class;'),
                        new Line(-1, 56, Line::ADDED, '$compte_rendu->object_id = $object_id;'),
                        new Line(-1, 57, Line::ADDED, '$compte_rendu->user_id = "";'),
                        new Line(-1, 58, Line::ADDED, '$compte_rendu->function_id = "";'),
                        new Line(-1, 59, Line::ADDED, '$compte_rendu->group_id = "";'),
                        new Line(-1, 60, Line::ADDED, '$compte_rendu->content_id = "";'),
                        new Line(-1, 61, Line::ADDED, '$compte_rendu->_ref_content->_id = "";'),
                        new Line(-1, 62, Line::ADDED, ''),
                        new Line(-1, 63, Line::ADDED, '$compte_rendu->_source = $compte_rendu->generateDocFromModel(null, $header_id, $footer_id);'),
                        new Line(-1, 64, Line::ADDED, ''),
                        new Line(-1, 65, Line::ADDED, '$msg = $compte_rendu->store();'),
                        new Line(-1, 66, Line::ADDED, ''),
                        new Line(-1, 67, Line::ADDED, 'if ($msg) {'),
                        new Line(-1, 68, Line::ADDED, '  CAppUI::setMsg($msg, UI_MSG_ERROR);'),
                        new Line(-1, 69, Line::ADDED, '}'),
                        new Line(-1, 70, Line::ADDED, 'else {'),
                        new Line(-1, 71, Line::ADDED, '  CAppUI::setMsg(CAppUI::tr("CCompteRendu-msg-create"));'),
                        new Line(-1, 72, Line::ADDED, '}'),
                        new Line(-1, 73, Line::ADDED, ''),
                        new Line(-1, 74, Line::ADDED, 'echo CAppUI::getMsg();'),
                    )
                )
            )
        );

        $this->assertEquals($file, $fileList[0]);
    }
}
