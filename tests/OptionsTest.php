<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\Options;

/**
 * @coversDefaultClass axy\backtrace\Trace
 */
class OptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testAccess()
    {
        $options = new Options(['tab' => 5]);
        $this->assertSame(5, $options->tab);
        $this->assertSame(1, $options['hStart']);
        $this->assertTrue(isset($options->nl));
        $this->assertTrue(isset($options['hHandler']));
        $this->assertFalse(isset($options->unk));
    }

    /**
     * @expectedException axy\magic\errors\ContainerReadOnly
     */
    public function testReadonly()
    {
        $options = new Options();
        $options->tab = 6;
    }

    /**
     * @expectedException axy\magic\errors\FieldNotExist
     */
    public function testNotFound()
    {
        $options = new Options();
        return $options->unk;
    }

    /**
     * @expectedException axy\magic\errors\FieldNotExist
     */
    public function testRigidly()
    {
        return new Options(['unk' => 1]);
    }
}
