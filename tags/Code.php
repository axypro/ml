<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

/**
 * Tag [CODE]
 *
 * @example [code:php echo 2 + 2;]
 *
 * @author Oleg Grigoriev
 */
class Code extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        if ($this->block) {
            $tag = $this->options['tag_block'];
            $css = $this->options['css_block'];
            $this->context->block->split = true;
        } else {
            $tag = $this->options['tag_inline'];
            $css = $this->options['css_inline'];
        }
        $res = [
            '<'.$tag
        ];
        if (($this->lang !== null) && ($this->options['attr_lang'] !== null)) {
            $attr = $this->options['attr_lang'];
            if ($attr === 'class') {
                if ($css !== null) {
                    $css = $this->lang.' '.$css;
                } else {
                    $css = $this->lang;
                }
            } else {
                $res[] .= ' '.$attr.'="'.$this->escape($this->lang).'"';
            }
        }
        if ($css !== null) {
            $res[] .= ' class="'.$this->escape($css).'"';
        }
        $res[] = '>'.($this->block ? "\n" : '');
        $res[] = $this->escape($this->code);
        $res[] = ($this->block ? "\n" : '').'</'.$tag.'>';
        return \implode('', $res);
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse()
    {
        if ($this->options['lang']) {
            $this->lang = $this->options['lang'];
        } else {
            $this->lang = $this->getArg(0, $this->options['default_lang']);
        }
        $this->code = $this->value;
        if ($this->block) {
            $this->createBlock = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function preparse()
    {
        $this->block = (\strpos($this->value, "\n") !== false);
        parent::preparse();
    }

    /**
     * {@inheritdoc}
     */
    protected $options = [
        'tag_block' => 'pre',
        'tag_inline' => 'code',
        'css_block' => null,
        'css_inline' => null,
        'attr_lang' => 'rel',
        'lang' => null,
        'default_lang' => null,
    ];

    /**
     * @var string
     */
    protected $lang;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var boolean
     */
    protected $block;
}
