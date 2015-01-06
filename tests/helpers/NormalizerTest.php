<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\helpers\Normalizer;
use axy\ml\Options;

/**
 * coversDefaultClass axy\ml\helpers\Normalizer
 */
class NormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::toParse
     * @dataProvider providerToParse
     * @param string $content
     * @param array $options
     * @param string $expected
     */
    public function testToParse($content, $options, $expected)
    {
        $options = new Options($options);
        $this->assertSame($expected, Normalizer::toParse($content, $options));
    }

    /**
     * @return array
     */
    public function providerToParse()
    {
        return [
            [
                "this is \n\tcontent",
                [],
                "this is \n    content",
            ],
            [
                "this\r\nis\n\r\n\r \n\tco\rntent",
                [
                    'nl' => "\r\n",
                    'tab' => 5,
                ],
                "this\nis\n\n \n     co\nntent",
            ],
        ];
    }

    /**
     * covers ::toResult
     * @dataProvider providerToResult
     * @param string $content
     * @param array $options
     * @param string $expected
     */
    public function testToResult($content, $options, $expected)
    {
        $options = new Options($options);
        $this->assertSame($expected, Normalizer::toResult($content, $options));
    }

    /**
     * @return array
     */
    public function providerToResult()
    {
        return [
            [
                "this \n is \n content",
                [],
                "this \n is \n content",
            ],
            [
                "this \n\n is \n content",
                [
                    'nl' => "\r\n",
                ],
                "this \r\n\r\n is \r\n content",
            ],
        ];
    }
}
