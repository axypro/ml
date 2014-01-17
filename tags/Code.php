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
        } else {
            $tag = $this->options['tag_inline'];
        }
        $res = [
            '<'.$tag
        ];
        if (($this->lang) && ($this->options['attr_lang'])) {
            $res[] .= ' '.$this->options['attr_lang'].'="'.$this->escape($this->lang).'"';
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
            $this->splitBlock = true;
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
