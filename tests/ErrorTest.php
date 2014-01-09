<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\Error;

/**
 * @coversDefaultClass axy\ml\Error
 */
class ErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__get
     * @covers ::__isset
     */
    public function testMagic()
    {
        $er = new Error(Error::TAG_NOT_CLOSED, 23, ['tag' => 'b']);
        $this->assertTrue(isset($er->code));
        $this->assertTrue(isset($er->line));
        $this->assertTrue(isset($er->data));
        $this->assertFalse(isset($er->unk));
        $this->assertSame(Error::TAG_NOT_CLOSED, $er->code);
        $this->assertSame(23, $er->line);
        $this->setExpectedException('axy\magic\errors\FieldNotExist');
        return $er->unk;
    }

    /**
     * @covers ::__get
     */
    public function testMessage()
    {
        $er1 = new Error(Error::TAG_NOT_CLOSED, 23, ['tag' => 'b']);
        $this->assertTrue(isset($er1->message));
        $this->assertSame('Tag [b] is not closed', $er1->message);

        $er2 = new Error(Error::TAG_NOT_CLOSED, 24);
        $this->assertSame('Tag [] is not closed', $er2->message);

        $er3 = new Error('unk');
        $this->assertSame('Unknown error', $er3->message);
    }

    /**
     * @covers ::toString
     */
    public function testToString()
    {
        $er = new Error(Error::TAG_NOT_CLOSED, 23, ['tag' => 'b']);
        $this->assertSame('Tag [b] is not closed on line 23', ''.$er);
    }

    /**
     * @expectedException axy\magic\errors\ContainerReadOnly
     */
    public function testReadOnly()
    {
        $er = new Error(Error::TAG_NOT_CLOSED, 23, ['tag' => 'b']);
        $er->line = 24;
    }
}
