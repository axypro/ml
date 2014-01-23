<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

use axy\callbacks\Callback;

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
        $params = $this->params;
        if ($params->block) {
            $this->context->block->split = true;
        }
        if ($params->html !== null) {
            return $params->html;
        }
        $html = '';
        $aeclass = false;
        if (($params->attr !== null) && ($params->lang !== null)) {
            $val = $this->escape($params->lang);
            if (($params->attr === 'class') && ($params->css !== null) && (!$params->block)) {
                $val .= ' '.$this->escape($params->css);
                $aeclass = true;
            }
            $alang = ' '.$params->attr.'="'.$val.'"';
        } else {
            $alang = '';
        }
        if (($params->css !== null) && (!$aeclass)) {
            $acss = ' class="'.$this->escape($params->css).'"';
        } else {
            $acss = '';
        }
        if ($params->block) {
            $html .= '<pre'.$acss.'><code'.$alang.'>'.$params->source."\n</code></pre>";
        } else {
            $html .= '<code'.$alang.$acss.'>'.$params->source.'</code>';
        }
        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return $this->params->plain;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse()
    {
        $params = $this->params;
        if ($this->options['lang']) {
            $params->lang = $this->options['lang'];
        } else {
            $params->lang = $this->getArg(0, $this->options['default_lang']);
        }
        $params->source = $this->escape($this->value);
        $params->plain = $this->value;
        $params->css = $params->block ? $this->options['css_block'] : $this->options['css_inline'];
        $this->params->attr = $this->options['attr_lang'];
        if ($this->params->block) {
            $this->createBlock = false;
        }
        if ($this->options['handler']) {
            Callback::call($this->options['handler'], [$this->params]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function preparse()
    {
        $this->params = (object)[
            'block' => (\strpos($this->value, "\n") !== false),
            'lang' => null,
            'source' => null,
            'plain' => null,
            'css' => null,
            'attr' => null,
            'html' => null,
        ];
        parent::preparse();
    }

    /**
     * {@inheritdoc}
     */
    protected $options = [
        'handler' => null,
        'css_block' => null,
        'css_inline' => null,
        'attr_lang' => 'rel',
        'lang' => null,
        'default_lang' => null,
    ];

    /**
     * @var object
     */
    private $params;
}
