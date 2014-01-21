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
     * @covers ::sortListByLine
     */
    public function testSortListByLine()
    {
        $e10 = new Error(Error::HEADER_EMPTY, 10);
        $e25 = new Error(Error::META_EMPTY, 25);
        $e8 = new Error(Error::TAG_INVALID, 8);
        $e17 = new Error(Error::TAG_NOT_CLOSED, 17);
        $e11 = new Error(Error::TAG_UNKNOWN, 11);
        $list = [$e10, $e25, $e8, $e17, $e11];
        $expected = [$e8, $e10, $e11, $e17, $e25];
        $this->assertEquals($expected, Error::sortListByLine($list));
    }

    /**
     * @covers ::__toString
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
