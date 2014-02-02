<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\Util;

/**
 * @coversDefaultClass axy\ml\Util
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers extractHead
     */
    public function testExtractHeadTitleOnly()
    {
        $content = \file_get_contents(__DIR__.'/nstst/util/head.ml');
        $result = Util::extractHead($content, false, false);
        $this->assertSame('This is title', $result->title);
        $this->assertNull($result->meta);
    }

    /**
     * @covers extractHead
     */
    public function testExtractHeadMeta()
    {
        $content = \file_get_contents(__DIR__.'/nstst/util/head.ml');
        $result = Util::extractHead($content, true, false);
        $this->assertSame('This is title', $result->title);
        $expected = [
            'one' => '1',
            'two' => true,
        ];
        $this->assertEquals($expected, $result->meta->getSource());
    }

    /**
     * @covers extractHead
     */
    public function testExtractHeadParser()
    {
        $content = \file_get_contents(__DIR__.'/nstst/util/head-parser.ml');
        $result1 = Util::extractHead($content, true, false);
        $this->assertNull($result1->title);
        $this->assertEmpty($result1->meta->getSource());
        $result2 = Util::extractHead($content, true, true);
        $this->assertSame('This is title', $result2->title);
        $expected = [
            'one' => '1',
            'two' => true,
            'three' => '3',
        ];
        $this->assertEquals($expected, $result2->meta->getSource());
    }
}
