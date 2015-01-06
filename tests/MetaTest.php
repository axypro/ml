<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\Meta;

/**
 * coversDefaultClass axy\ml\Meta
 */
class MetaTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $meta = new Meta();
        $meta->title = 'This is title';
        $meta['title'] = 'This is new title';
        $meta->url = 'http://url';
        $this->assertSame('This is new title', $meta->title);
        $this->assertSame('http://url', $meta['url']);
        $this->assertSame(null, $meta->unk);
        $this->assertSame(null, $meta->value('unk'));
        $this->assertSame('def', $meta->value('unk', 'def'));
        $this->assertSame('This is new title', $meta->value('title'));
        $this->assertSame('This is new title', $meta->value('title', 'def'));
    }
}
