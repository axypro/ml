<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\Parser;

/**
 * @coversDefaultClass axy\ml\Result
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $axyml = $this->getFile('base.axyml');
        $html = $this->getFile('base.html');
        $plain = $this->getFile('base.txt');
        $parser = new Parser();
        $result = $parser->parse($axyml);
        $this->assertSame($html, \rtrim($result->html));
        $this->assertSame($plain, \rtrim($result->plain));
        $this->assertFalse($result->isCutted);
        $this->assertSame('basic, test', $result->meta->keywords);
        $this->assertSame('Title of the document', $result->title);
        $errors = [];
        foreach ($result->errors as $error) {
            $errors[] = (string)$error;
        }
        $expected = [
            'Header is empty on line 4',
            'Meta is empty on line 8',
            'Invalid [img]: empty src on line 15',
            'Unknown tag [unknown] on line 26',
            'Tag [br] is not closed on line 31',
        ];
        $this->assertEquals($expected, $errors);
        $expected = [
            [
                'content' => 'Title of the document',
                'level' => 1,
                'name' => null,
            ],
            [
                'content' => 'Subtitle',
                'level' => 2,
                'name' => 'cut',
            ],
            [
                'content' => 'Header of level 3',
                'level' => 3,
                'name' => null,
            ],
            [
                'content' => '2 level > header',
                'level' => 2,
                'name' => null,
            ],
        ];
        $this->assertEquals($expected, $result->getHeaders());
        $expected = [
            [
                'content' => 'Title of the document',
                'level' => 1,
                'name' => null,
            ],
            [
                'content' => 'Subtitle',
                'level' => 2,
                'name' => 'cut',
            ],
            [
                'content' => '2 level > header',
                'level' => 2,
                'name' => null,
            ],
        ];
        $this->assertEquals($expected, $result->getHeaders(2));
    }

    public function testCut()
    {
        $axyml = $this->getFile('base.axyml');
        $html1 = $this->getFile('base.html');
        $html2 = $this->getFile('cut.html');
        $parser = new Parser();
        $result1 = $parser->parse($axyml, 'unk');
        $this->assertSame($html1, \rtrim($result1->html));
        $this->assertFalse($result1->isCutted);
        $result2 = $parser->parse($axyml, 'anchor');
        $this->assertSame($html2, \rtrim($result2->html));
        $this->assertTrue($result2->isCutted);
    }

    /**
     * @param string $fn
     * @return string
     */
    private function getFile($fn)
    {
        if (!isset($this->files[$fn])) {
            $filename = __DIR__.'/nstst/parse/'.$fn;
            $this->files[$fn] = \rtrim(\file_get_contents($filename));
        }
        return $this->files[$fn];
    }

    /**
     * @var array
     */
    private $files = [];
}
