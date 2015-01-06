<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\ProfilerTest;

use axy\ml\Parser;

/**
 * coversDefaultClass axy\ml\Profiler
 */
class ProfilerTest extends \PHPUnit_Framework_TestCase
{
    public function testProfiler()
    {
        $parser = new Parser();
        $result = $parser->parse('content');
        $profiler = $result->profiler;
        $this->assertInstanceOf('axy\ml\Profiler', $profiler);
        $this->assertInternalType('float', $profiler->tokenize);
        $this->assertNull($profiler->html);
        $this->assertNull($profiler->plain);
        $r = $result->html;
        $this->assertInternalType('float', $profiler->tokenize);
        $this->assertInternalType('float', $profiler->html);
        $this->assertNull($profiler->plain);
        $r = $result->plain;
        $this->assertInternalType('float', $profiler->tokenize);
        $this->assertInternalType('float', $profiler->html);
        $this->assertInternalType('float', $profiler->plain);
        return $r;
    }
}
