<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\Context;
use axy\ml\Result;
use axy\ml\Options;
use axy\ml\TagsList;
use axy\ml\helpers\Tokenizer;

/**
 * coversDefaultClass axy\ml\Context
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function testContext()
    {
        $options = new Options();
        $tags = new TagsList();
        $tokenizer = new Tokenizer('');
        $result = new Result($tokenizer, $options, $tags);
        $context = new Context($result, $options, $tags, 12);
        $this->assertTrue(isset($context->result));
        $this->assertTrue(isset($context->options));
        $this->assertTrue(isset($context->tags));
        $this->assertTrue(isset($context->custom));
        $this->assertSame($result, $context->result);
        $this->assertSame($options, $context->options);
        $this->assertSame($tags, $context->tags);
        $this->assertSame(12, $context->custom);
        $this->setExpectedException('axy\magic\errors\ContainerReadOnly');
        $context->tags = 1;
    }
}
