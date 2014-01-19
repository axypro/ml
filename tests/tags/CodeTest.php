<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\Code;

/**
 * @coversDefaultClass axy\ml\tags\Code
 */
class CodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerCode
     * @param string $content
     * @param array $options
     * @param string $html
     * @param string $plain
     * @param boolean $split
     * @param boolean $create
     */
    public function testCode($content, $options, $html, $plain, $split, $create)
    {
        $tag = new Code('code', $content, $options);
        $this->assertSame($html, $tag->getHTML());
        $this->assertSame($plain, $tag->getPlain());
        $this->assertSame($split, $tag->shouldSplitBlock());
        $this->assertSame($create, $tag->shouldCreateBlock());
    }

    /**
     * @return array
     */
    public function providerCode()
    {
        return [
            [
                ':php echo 5 > 4;',
                null,
                '<code rel="php">echo 5 &gt; 4;</code>',
                'echo 5 > 4;',
                false,
                true,
            ],
            [
                ' echo 2 + 2;',
                null,
                '<code>echo 2 + 2;</code>',
                'echo 2 + 2;',
                false,
                true,
            ],
            [
                ' echo 2 + 2;',
                ['default_lang' => 'php'],
                '<code rel="php">echo 2 + 2;</code>',
                'echo 2 + 2;',
                false,
                true,
            ],
            [
                ":js  \n  x = 2;\n  y = 2;\n",
                null,
                "<pre rel=\"js\">\n  x = 2;\n  y = 2;\n\n</pre>",
                "  x = 2;\n  y = 2;\n",
                true,
                false,
            ],
            [
                "  \n  x = 2;\n  y = 2;\n",
                null,
                "<pre>\n  x = 2;\n  y = 2;\n\n</pre>",
                "  x = 2;\n  y = 2;\n",
                true,
                false,
            ],
            [
                ':php   echo 2 + 2;',
                ['tag_block' => 'div', 'tag_inline' => 'span', 'attr_lang' => 'lang'],
                '<span lang="php">echo 2 + 2;</span>',
                'echo 2 + 2;',
                false,
                true,
            ],
            [
                ":php \necho 2 + 2;",
                ['tag_block' => 'div', 'tag_inline' => 'span', 'attr_lang' => 'lang'],
                "<div lang=\"php\">\necho 2 + 2;\n</div>",
                'echo 2 + 2;',
                true,
                false,
            ],
            [
                " \necho 2 + 2;",
                ['lang' => 'php'],
                "<pre rel=\"php\">\necho 2 + 2;\n</pre>",
                'echo 2 + 2;',
                true,
                false,
            ],
            [
                ":javascript \necho 2 + 2;",
                ['lang' => 'php'],
                "<pre rel=\"php\">\necho 2 + 2;\n</pre>",
                'echo 2 + 2;',
                true,
                false,
            ],
            [
                ":php \necho 2 + 2;",
                ['css_block' => 'cblock', 'css_inline' => 'cinline'],
                "<pre class=\"cblock\" rel=\"php\">\necho 2 + 2;\n</pre>",
                'echo 2 + 2;',
                true,
                false,
            ],
            [
                ":php echo 2 + 2;",
                ['css_block' => 'cblock', 'css_inline' => 'cinline'],
                '<code class="cinline" rel="php">echo 2 + 2;</code>',
                'echo 2 + 2;',
                false,
                true,
            ],
        ];
    }
}
