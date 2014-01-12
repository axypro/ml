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
            'Unknown tag [unknown] on line 26',
            'Tag [br] is not closed on line 31',
        ];
        $this->assertEquals($expected, $errors);
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
