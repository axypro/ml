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
        $options = [
            'content' => \file_get_contents(__DIR__.'/nstst/util/head.ml'),
            'meta' => false,
            'parser' => false,
        ];
        $result = Util::extractHead($options);
        $this->assertSame('This is title', $result->title);
        $this->assertNull($result->meta);
    }

    /**
     * @covers extractHead
     */
    public function testExtractHeadMeta()
    {
        $options = [
            'content' => \file_get_contents(__DIR__.'/nstst/util/head.ml'),
            'parser' => false,
        ];
        $result = Util::extractHead($options);
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
        $options1 = [
            'content' => \file_get_contents(__DIR__.'/nstst/util/head-parser.ml'),
            'parser' => false,
        ];
        $result1 = Util::extractHead($options1);
        $this->assertNull($result1->title);
        $this->assertEmpty($result1->meta->getSource());
        $options2 = [
            'content' => \file_get_contents(__DIR__.'/nstst/util/head-parser.ml'),
        ];
        $result2 = Util::extractHead($options2);
        $this->assertSame('This is title', $result2->title);
        $expected = [
            'one' => '1',
            'two' => true,
            'three' => '3',
        ];
        $this->assertEquals($expected, $result2->meta->getSource());
    }

    /**
     * @covers extractHead
     */
    public function testExtractHeadFile()
    {
        $options = [
            'filename' => __DIR__.'/nstst/util/head.ml',
            'parser' => false,
        ];
        $result = Util::extractHead($options);
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
    public function testExtractHeadParserFile()
    {
        $options1 = [
            'filename' => __DIR__.'/nstst/util/head-parser.ml',
            'parser' => false,
        ];
        $result1 = Util::extractHead($options1);
        $this->assertNull($result1->title);
        $this->assertEmpty($result1->meta->getSource());
        $options2 = [
            'filename' => __DIR__.'/nstst/util/head-parser.ml',
        ];
        $result2 = Util::extractHead($options2);
        $this->assertSame('This is title', $result2->title);
        $expected = [
            'one' => '1',
            'two' => true,
            'three' => '3',
        ];
        $this->assertEquals($expected, $result2->meta->getSource());
    }
}
