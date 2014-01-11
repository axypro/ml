<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\helpers;

use axy\ml\helpers\Config;

/**
 * @coversDefaultClass axy\ml\helpers\Config
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getOptions
     */
    public function testGetOptions()
    {
        $options = Config::getOptions();
        $this->assertSame("\n", $options['nl']);
        $this->assertEquals($options, Config::getOptions());
    }

    /**
     * @covers ::getTags
     */
    public function testGetTags()
    {
        $tags = Config::getTags();
        $this->assertSame('HtmlTag', $tags['table']);
        $this->assertEquals($tags, Config::getTags());
    }
}
