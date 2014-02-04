<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\Code;
use axy\ml\tests\nstst\Factory;

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
        $context = Factory::createContext();
        $tag = new Code('code', $content, $options, $context);
        $this->assertSame($html, $tag->getHTML());
        $this->assertSame($plain, $tag->getPlain());
        $this->assertSame($split, $context->block->split);
        $this->assertSame($create, $context->block->create);
    }

    /**
     * @return array
     */
    public function providerCode()
    {
        $handler = function ($params) {
            $params->plain .= '!';
            if ($params->block) {
                $params->html = '<div rel="'.$params->lang.'" class="pre">'.$params->source.'</div>';
            } else {
                $params->html = '<span rel="'.$params->lang.'">'.$params->source.'</span>';
            }
        };
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
                ":js  \n  x = 2;\n  y = 2;",
                null,
                "<pre><code rel=\"js\">  x = 2;\n  y = 2;\n</code></pre>",
                "  x = 2;\n  y = 2;",
                true,
                false,
            ],
            [
                "  \n  x = 2;\n  y = 2;",
                null,
                "<pre><code>  x = 2;\n  y = 2;\n</code></pre>",
                "  x = 2;\n  y = 2;",
                true,
                false,
            ],
            [
                " \necho 2 + 2;",
                ['lang' => 'php'],
                "<pre><code rel=\"php\">echo 2 + 2;\n</code></pre>",
                'echo 2 + 2;',
                true,
                false,
            ],
            [
                ":javascript \necho 2 + 2;",
                ['lang' => 'php'],
                "<pre><code rel=\"php\">echo 2 + 2;\n</code></pre>",
                'echo 2 + 2;',
                true,
                false,
            ],
            [
                ":php \necho 2 + 2;",
                ['css_block' => 'cblock', 'css_inline' => 'cinline'],
                "<pre class=\"cblock\"><code rel=\"php\">echo 2 + 2;\n</code></pre>",
                'echo 2 + 2;',
                true,
                false,
            ],
            [
                ":php echo 2 + 2;",
                ['css_block' => 'cblock', 'css_inline' => 'cinline'],
                '<code rel="php" class="cinline">echo 2 + 2;</code>',
                'echo 2 + 2;',
                false,
                true,
            ],
            [
                ':0 0',
                ['css_inline' => '0'],
                '<code rel="0" class="0">0</code>',
                '0',
                false,
                true,
            ],
            [
                ':php echo 2 + 2;',
                ['css_inline' => 'cinline', 'css_block' => 'cblock', 'attr_lang' => 'class'],
                '<code class="php cinline">echo 2 + 2;</code>',
                'echo 2 + 2;',
                false,
                true,
            ],
            [
                ":php\necho 2 + 2;",
                ['css_inline' => 'cinline', 'css_block' => 'cblock', 'attr_lang' => 'class'],
                "<pre class=\"cblock\"><code class=\"php\">echo 2 + 2;\n</code></pre>",
                'echo 2 + 2;',
                true,
                false,
            ],
            [
                ":php\necho 1;\necho 1 > 2;",
                ['handler' => $handler],
                "<div rel=\"php\" class=\"pre\">echo 1;\necho 1 &gt; 2;</div>",
                "echo 1;\necho 1 > 2;!",
                true,
                false,
            ],
            [
                ":php echo 1 > 2",
                ['handler' => $handler],
                '<span rel="php">echo 1 &gt; 2</span>',
                "echo 1 > 2!",
                false,
                true,
            ],
            [
                ' [B] ',
                null,
                '<code>[B]</code>',
                '[B]',
                false,
                true,
            ],
        ];
    }
}
